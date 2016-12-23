<?php
require('js/jQuery File Upload/php/UploadHandler.php');

class CInc_Job_Fil_Uploadhandler extends UploadHandler {

  protected function upcount_name_callback($matches) {
    $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
    $ext = isset($matches[2]) ? $matches[2] : '';
    return '_'.$index.$ext;
  }

  protected function upcount_name($name) {
    return preg_replace_callback(
      '/(?:(?:\_([\d]+))?(\.[^.]+))?$/',
      array($this, 'upcount_name_callback'),
      $name,
      1
    );
  }

  protected function get_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
    switch ($this -> options['sub']) {
      case 'doc':
      case 'pixelboxx':
        if ($this -> options['overwrite'] == TRUE) {
          return $this -> trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range);
        } else {
          $lFilename = $this -> trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range);
          return $this -> get_unique_filename($file_path, $lFilename, $size, $type, $error, $index, $content_range);
        }
      break;
      default:
        $lExtention = pathinfo($name, PATHINFO_EXTENSION);

        $lFilenamePrefix = '';
        if (strtoupper(substr($this -> options['jid'], 0, 1)) == 'A') {
            $lFilenameStem = $this -> options['jid'];
        } else {
            $lFilenameStem = intval($this -> options['jid']);
        }
        $lFilenamePostfix = '';
        $lFilenameExtention = $lExtention ? '.'.$lExtention : '';

        $lFilename = $lFilenamePrefix.$lFilenameStem.$lFilenamePostfix.$lFilenameExtention;

        return $lFilename;
      break;
    }
  }

  protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
      $index = null, $content_range = null) {
    $file = new \stdClass();
    $file->name = $this->get_file_name($uploaded_file, $name, $size, $type, $error,
      $index, $content_range);

    // START BUGFIX: this is due to PHP bug #46990
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $file->name = utf8_decode($file->name);
    }
    // STOP BUGFIX

    $file->size = $this->fix_integer_overflow((int)$size);
    $file->type = $type;
    if ($this->validate($uploaded_file, $file, $error, $index)) {
      $this->handle_form_data($file, $index);
      $upload_dir = $this->get_upload_path();
      if (!is_dir($upload_dir)) {
        mkdir($upload_dir, $this->options['mkdir_mode'], true);
      }
      $file_path = $this->get_upload_path($file->name);

      $append_file = $content_range && is_file($file_path) &&
        $file->size > $this->get_file_size($file_path);
      if ($uploaded_file && is_uploaded_file($uploaded_file)) {
        // multipart/formdata uploads (POST method uploads)
        if ($append_file) {
          file_put_contents(
            $file_path,
            fopen($uploaded_file, 'r'),
            FILE_APPEND
          );
        } else {
          move_uploaded_file($uploaded_file, $file_path);
        }
      } else {
        // Non-multipart uploads (PUT method support)
        file_put_contents(
          $file_path,
          fopen('php://input', 'r'),
          $append_file ? FILE_APPEND : 0
        );
      }
      $file_size = $this->get_file_size($file_path, $append_file);
      if ($file_size === $file->size) {
        $file->url = $this->get_download_url($file->name);
        if ($this->is_valid_image_file($file_path)) {
          $this->handle_image_file($file_path, $file);
        }
      } else {
        $file->size = $file_size;
        if (!$content_range && $this->options['discard_aborted_uploads']) {
          unlink($file_path);
          $file->error = $this->get_error_message('abort');
        }
      }
      $this->set_additional_file_properties($file);
    }

    // START BUGFIX: this is due to PHP bug #46990
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $file->name = utf8_encode($file->name);
    }
    // STOP BUGFIX

    return $file;
  }

  public function generate_response($content, $print_response = true) {
    $this->response = $content;
    if ($print_response) {
      $json = json_encode($content);
      // START SECURITY ISSUE: Open redirect / Header manipulation (Code Review 5Flow by ERNW, 9th Febuary 2015)
      $redirect = stripslashes($this->get_query_param('redirect'));
      if ($redirect) {
        $redirect = 'index.php?act=hom-wel';

        $this->header('Location: '.sprintf($redirect, rawurlencode($json)));
        return;
      }
      // STOP SECURITY ISSUE
      $this->head();
      if ($this->get_server_var('HTTP_CONTENT_RANGE')) {
        $files = isset($content[$this->options['param_name']]) ?
          $content[$this->options['param_name']] : null;
        if ($files && is_array($files) && is_object($files[0]) && $files[0]->size) {
          $this->header('Range: 0-'.(
            $this->fix_integer_overflow((int)$files[0]->size) - 1
          ));
        }
      }
      $this->body($json);
    }
    return $content;
}
}