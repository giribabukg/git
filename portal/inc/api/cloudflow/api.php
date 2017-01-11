<?php
namespace cloudflow
{

  class api_helper
  {
    private static $session = null;
    private static $address = null;

    public static function set_address($address)
    {
      api_helper::$address = $address . '/portal.cgi';
    }

    public static function set_session($session)
    {
      api_helper::$session = $session;
    }

    public static function call($method, $params)
    {
      $request = $params;
      $request['method'] = $method;
      if (api_helper::$session !== null)
        $request['session'] = api_helper::$session;
      $curl_handle = curl_init(api_helper::$address);
      curl_setopt($curl_handle, CURLOPT_POST, 1);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($request));
      //echo BR.json_encode($request).BR;
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
      return json_decode(curl_exec($curl_handle), true);
    }
  };

  function set_address($address)
  {
    \cloudflow\api_helper::set_address($address);
  }

  function set_session($session)
  {
    \cloudflow\api_helper::set_session($session);
  }
}

namespace cloudflow\portal
{
}

namespace cloudflow\portal
{
  function get_proofscope_url($proofscope_id)
  {
    return \cloudflow\api_helper::call('portal.get_proofscope_url', array('proofscope_id' => $proofscope_id));
  }
}

namespace cloudflow\portal
{
  function get_stats()
  {
    return \cloudflow\api_helper::call('portal.get_stats', array());
  }
}

namespace cloudflow\portal
{
  function get_active_users()
  {
    return \cloudflow\api_helper::call('portal.get_active_users', array());
  }
}

namespace cloudflow\portal
{
  function get_workers_pending()
  {
    return \cloudflow\api_helper::call('portal.get_workers_pending', array());
  }
}

namespace cloudflow\portal
{
  function version()
  {
    return \cloudflow\api_helper::call('portal.version', array());
  }
}

namespace cloudflow\portal
{
  function setup()
  {
    return \cloudflow\api_helper::call('portal.setup', array());
  }
}

namespace cloudflow\portal
{
  function send_test_mail()
  {
    return \cloudflow\api_helper::call('portal.send_test_mail', array());
  }
}

namespace cloudflow\portal
{
  function check_file_store($path, $test_file)
  {
    return \cloudflow\api_helper::call('portal.check_file_store', array('path' => $path, 'test_file' => $test_file));
  }
}

namespace cloudflow\portal
{
  function flush($collection)
  {
    return \cloudflow\api_helper::call('portal.flush', array('collection' => $collection));
  }
}

namespace cloudflow\portal
{
  function remove_filestore($filestore)
  {
    return \cloudflow\api_helper::call('portal.remove_filestore', array('filestore' => $filestore));
  }
}

namespace cloudflow\portal
{
  function drop_blue_collar_definitions($mode)
  {
    return \cloudflow\api_helper::call('portal.drop_blue_collar_definitions', array('mode' => $mode));
  }
}

namespace cloudflow\portal
{
  function scan_addons()
  {
    return \cloudflow\api_helper::call('portal.scan_addons', array());
  }
}

namespace cloudflow\auth
{
}

namespace cloudflow\auth
{
  function login($user_name, $user_pass)
  {
    return \cloudflow\api_helper::call('auth.login', array('user_name' => $user_name, 'user_pass' => $user_pass));
  }
}

namespace cloudflow\auth
{
  function create_session($user_name, $options)
  {
    return \cloudflow\api_helper::call('auth.create_session', array('user_name' => $user_name, 'options' => $options));
  }
}

namespace cloudflow\auth
{
  function create_session_with_expiry($user_name, $user_pass, $expiry)
  {
    return \cloudflow\api_helper::call('auth.create_session_with_expiry', array('user_name' => $user_name, 'user_pass' => $user_pass, 'expiry' => $expiry));
  }
}

namespace cloudflow\auth
{
  function get_current_user()
  {
    return \cloudflow\api_helper::call('auth.get_current_user', array());
  }
}

namespace cloudflow\auth
{
  function generate_oauth2_url($provider)
  {
    return \cloudflow\api_helper::call('auth.generate_oauth2_url', array('provider' => $provider));
  }
}

namespace cloudflow\approvalchains
{
}

namespace cloudflow\approvalchains
{
  function remove($approvalchain_id)
  {
    return \cloudflow\api_helper::call('approvalchains.remove', array('approvalchain_id' => $approvalchain_id));
  }
}

namespace cloudflow\approvalchains
{
  function list_all()
  {
    return \cloudflow\api_helper::call('approvalchains.list_all', array());
  }
}

namespace cloudflow\approvalchains
{
  function update($approvalchain_id, $active, $steps, $name, $scope, $template)
  {
    return \cloudflow\api_helper::call('approvalchains.update', array('approvalchain_id' => $approvalchain_id, 'active' => $active, 'steps' => $steps, 'name' => $name, 'scope' => $scope, 'template' => $template));
  }
}

namespace cloudflow\approvalchains
{
  function add($active, $steps, $name, $scope, $template)
  {
    return \cloudflow\api_helper::call('approvalchains.add', array('active' => $active, 'steps' => $steps, 'name' => $name, 'scope' => $scope, 'template' => $template));
  }
}

namespace cloudflow\approvalchains
{
  function apply_approvalchain_to_asset($approvalchain_id, $url)
  {
    return \cloudflow\api_helper::call('approvalchains.apply_approvalchain_to_asset', array('approvalchain_id' => $approvalchain_id, 'url' => $url));
  }
}

namespace cloudflow\approvalchains
{
  function get_approvalchain_for_asset($url)
  {
    return \cloudflow\api_helper::call('approvalchains.get_approvalchain_for_asset', array('url' => $url));
  }
}

namespace cloudflow\approvalchains
{
  function cancel_asset_approvalchain($url)
  {
    return \cloudflow\api_helper::call('approvalchains.cancel_asset_approvalchain', array('url' => $url));
  }
}

namespace cloudflow\attributes
{
}

namespace cloudflow\attributes
{
  function remove($attribute_id)
  {
    return \cloudflow\api_helper::call('attributes.remove', array('attribute_id' => $attribute_id));
  }
}

namespace cloudflow\attributes
{
  function list_all()
  {
    return \cloudflow\api_helper::call('attributes.list_all', array());
  }
}

namespace cloudflow\attributes
{
  function list_for_contact($contact_id)
  {
    return \cloudflow\api_helper::call('attributes.list_for_contact', array('contact_id' => $contact_id));
  }
}

namespace cloudflow\attributes
{
  function add_to_contact_by_id($contact_id, $attribute_id)
  {
    return \cloudflow\api_helper::call('attributes.add_to_contact_by_id', array('contact_id' => $contact_id, 'attribute_id' => $attribute_id));
  }
}

namespace cloudflow\attributes
{
  function add_to_contact_by_name($contact_id, $attribute_name)
  {
    return \cloudflow\api_helper::call('attributes.add_to_contact_by_name', array('contact_id' => $contact_id, 'attribute_name' => $attribute_name));
  }
}

namespace cloudflow\attributes
{
  function change_name($attribute_id, $attribute_name)
  {
    return \cloudflow\api_helper::call('attributes.change_name', array('attribute_id' => $attribute_id, 'attribute_name' => $attribute_name));
  }
}

namespace cloudflow\attributes
{
  function add($attribute_name)
  {
    return \cloudflow\api_helper::call('attributes.add', array('attribute_name' => $attribute_name));
  }
}

namespace cloudflow\scopes
{
}

namespace cloudflow\scopes
{
  function remove($scope_id)
  {
    return \cloudflow\api_helper::call('scopes.remove', array('scope_id' => $scope_id));
  }
}

namespace cloudflow\scopes
{
  function list_all()
  {
    return \cloudflow\api_helper::call('scopes.list_all', array());
  }
}

namespace cloudflow\scopes
{
  function update($scope_id, $name, $filter, $welcomepage)
  {
    return \cloudflow\api_helper::call('scopes.update', array('scope_id' => $scope_id, 'name' => $name, 'filter' => $filter, 'welcomepage' => $welcomepage));
  }
}

namespace cloudflow\scopes
{
  function add($name, $filter, $welcomepage)
  {
    return \cloudflow\api_helper::call('scopes.add', array('name' => $name, 'filter' => $filter, 'welcomepage' => $welcomepage));
  }
}

namespace cloudflow\scopes
{
  function get($name, $add_if_missing)
  {
    return \cloudflow\api_helper::call('scopes.get', array('name' => $name, 'add_if_missing' => $add_if_missing));
  }
}

namespace cloudflow\assets
{
}

namespace cloudflow\assets
{
  function get($_id)
  {
    return \cloudflow\api_helper::call('assets.get', array('_id' => $_id));
  }
}

namespace cloudflow\assets
{
  function get_with_url($url, $sub)
  {
    return \cloudflow\api_helper::call('assets.get_with_url', array('url' => $url, 'sub' => $sub));
  }
}

namespace cloudflow\assets
{
  function get_cloudflow_url($url, $sub, $type)
  {
    return \cloudflow\api_helper::call('assets.get_cloudflow_url', array('url' => $url, 'sub' => $sub, 'type' => $type));
  }
}

namespace cloudflow\assets
{
  function reset_metadata($url)
  {
    return \cloudflow\api_helper::call('assets.reset_metadata', array('url' => $url));
  }
}

namespace cloudflow\assets
{
  function reset_thumb($url)
  {
    return \cloudflow\api_helper::call('assets.reset_thumb', array('url' => $url));
  }
}

namespace cloudflow\assets
{
  function reset_render($url)
  {
    return \cloudflow\api_helper::call('assets.reset_render', array('url' => $url));
  }
}

namespace cloudflow\templates
{
}

namespace cloudflow\templates
{
  function remove($template_id)
  {
    return \cloudflow\api_helper::call('templates.remove', array('template_id' => $template_id));
  }
}

namespace cloudflow\templates
{
  function list_all()
  {
    return \cloudflow\api_helper::call('templates.list_all', array());
  }
}

namespace cloudflow\templates
{
  function update($template_id, $name, $language, $subject, $message)
  {
    return \cloudflow\api_helper::call('templates.update', array('template_id' => $template_id, 'name' => $name, 'language' => $language, 'subject' => $subject, 'message' => $message));
  }
}

namespace cloudflow\templates
{
  function add($name, $language, $subject, $message)
  {
    return \cloudflow\api_helper::call('templates.add', array('name' => $name, 'language' => $language, 'subject' => $subject, 'message' => $message));
  }
}

namespace cloudflow\templates
{
  function fill_template($template_name, $template_language, $variable_map)
  {
    return \cloudflow\api_helper::call('templates.fill_template', array('template_name' => $template_name, 'template_language' => $template_language, 'variable_map' => $variable_map));
  }
}

namespace cloudflow\panzer
{
}

namespace cloudflow\panzer
{
  function exportpdf($asset_id, $filepath)
  {
    return \cloudflow\api_helper::call('panzer.exportpdf', array('asset_id' => $asset_id, 'filepath' => $filepath));
  }
}

namespace cloudflow\panzer
{
  function get_registration($email)
  {
    return \cloudflow\api_helper::call('panzer.get_registration', array('email' => $email));
  }
}

namespace cloudflow\panzer
{
  function list_registrations()
  {
    return \cloudflow\api_helper::call('panzer.list_registrations', array());
  }
}

namespace cloudflow\panzer
{
  function disable_registration()
  {
    return \cloudflow\api_helper::call('panzer.disable_registration', array());
  }
}

namespace cloudflow\panzer\costcalc
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.costcalc.list', array());
  }
}

namespace cloudflow\panzer\device
{
  function get($id)
  {
    return \cloudflow\api_helper::call('panzer.device.get', array('id' => $id));
  }
}

namespace cloudflow\panzer\device
{
  function save($record)
  {
    return \cloudflow\api_helper::call('panzer.device.save', array('record' => $record));
  }
}

namespace cloudflow\panzer\device
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.device.list', array());
  }
}

namespace cloudflow\panzer\jobinfo
{
  function sendpjl($ip, $port, $command)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.sendpjl', array('ip' => $ip, 'port' => $port, 'command' => $command));
  }
}

namespace cloudflow\panzer\jobinfo
{
  function get($id)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.get', array('id' => $id));
  }
}

namespace cloudflow\panzer\jobinfo
{
  function remove($id)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.remove', array('id' => $id));
  }
}

namespace cloudflow\panzer\jobinfo
{
  function save($record)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.save', array('record' => $record));
  }
}

namespace cloudflow\panzer\jobinfo
{
  function send_raw($ip, $port, $command)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.send_raw', array('ip' => $ip, 'port' => $port, 'command' => $command));
  }
}

namespace cloudflow\panzer\jobinfo
{
  function setdatablob($id, $blob)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.setdatablob', array('id' => $id, 'blob' => $blob));
  }
}

namespace cloudflow\panzer\jobinfo
{
  function getdatablob($id)
  {
    return \cloudflow\api_helper::call('panzer.jobinfo.getdatablob', array('id' => $id));
  }
}

namespace cloudflow\panzer\layoutdb
{
  function get($id)
  {
    return \cloudflow\api_helper::call('panzer.layoutdb.get', array('id' => $id));
  }
}

namespace cloudflow\panzer\layoutdb
{
  function remove($id)
  {
    return \cloudflow\api_helper::call('panzer.layoutdb.remove', array('id' => $id));
  }
}

namespace cloudflow\panzer\layoutdb
{
  function save($record)
  {
    return \cloudflow\api_helper::call('panzer.layoutdb.save', array('record' => $record));
  }
}

namespace cloudflow\panzer\layoutdb
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.layoutdb.list', array());
  }
}

namespace cloudflow\panzer\lmsstate
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.lmsstate.list', array());
  }
}

namespace cloudflow\panzer\media
{
  function remove($id)
  {
    return \cloudflow\api_helper::call('panzer.media.remove', array('id' => $id));
  }
}

namespace cloudflow\panzer\media
{
  function save($record)
  {
    return \cloudflow\api_helper::call('panzer.media.save', array('record' => $record));
  }
}

namespace cloudflow\panzer\media
{
  function get($id, $size_width)
  {
    return \cloudflow\api_helper::call('panzer.media.get', array('id' => $id, 'size_width' => $size_width));
  }
}

namespace cloudflow\panzer\media
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.media.list', array());
  }
}

namespace cloudflow\panzer\layout
{
  function createlayout($parameters)
  {
    return \cloudflow\api_helper::call('panzer.layout.createlayout', array('parameters' => $parameters));
  }
}

namespace cloudflow\panzer\layout
{
  function createlayoutvdp($parameters)
  {
    return \cloudflow\api_helper::call('panzer.layout.createlayoutvdp', array('parameters' => $parameters));
  }
}

namespace cloudflow\panzer\layout
{
  function updateeyemarks($layout, $eyemarks)
  {
    return \cloudflow\api_helper::call('panzer.layout.updateeyemarks', array('layout' => $layout, 'eyemarks' => $eyemarks));
  }
}

namespace cloudflow\panzer\layout
{
  function applymargins($layout, $margins, $minheight)
  {
    return \cloudflow\api_helper::call('panzer.layout.applymargins', array('layout' => $layout, 'margins' => $margins, 'minheight' => $minheight));
  }
}

namespace cloudflow\panzer\layout
{
  function openfile()
  {
    return \cloudflow\api_helper::call('panzer.layout.openfile', array());
  }
}

namespace cloudflow\panzer\layout
{
  function inkcoverages($path)
  {
    return \cloudflow\api_helper::call('panzer.layout.inkcoverages', array('path' => $path));
  }
}

namespace cloudflow\panzer\layout
{
  function savelayoutdb($layout)
  {
    return \cloudflow\api_helper::call('panzer.layout.savelayoutdb', array('layout' => $layout));
  }
}

namespace cloudflow\panzer\layout
{
  function resoterlayoutdb($id)
  {
    return \cloudflow\api_helper::call('panzer.layout.resoterlayoutdb', array('id' => $id));
  }
}

namespace cloudflow\panzer\layout
{
  function savelayout($parameters)
  {
    return \cloudflow\api_helper::call('panzer.layout.savelayout', array('parameters' => $parameters));
  }
}

namespace cloudflow\panzer\layout
{
  function sendlayout($layout)
  {
    return \cloudflow\api_helper::call('panzer.layout.sendlayout', array('layout' => $layout));
  }
}

namespace cloudflow\panzer\preference
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.preference.list', array());
  }
}

namespace cloudflow\panzer\preference
{
  function save($record)
  {
    return \cloudflow\api_helper::call('panzer.preference.save', array('record' => $record));
  }
}

namespace cloudflow\panzer\printqueue
{
  function list_all()
  {
    return \cloudflow\api_helper::call('panzer.printqueue.list', array());
  }
}

namespace cloudflow\panzer\printqueue
{
  function remove($id)
  {
    return \cloudflow\api_helper::call('panzer.printqueue.remove', array('id' => $id));
  }
}

namespace cloudflow\panzer\printqueue
{
  function get($id)
  {
    return \cloudflow\api_helper::call('panzer.printqueue.get', array('id' => $id));
  }
}

namespace cloudflow\panzer\printqueue
{
  function save($jobdata)
  {
    return \cloudflow\api_helper::call('panzer.printqueue.save', array('jobdata' => $jobdata));
  }
}

namespace cloudflow\utils
{
}

namespace cloudflow\utils\xml
{
  function read($xml)
  {
    return \cloudflow\api_helper::call('utils.xml.read', array('xml' => $xml));
  }
}

namespace cloudflow\utils\xml
{
  function read_url($file_url)
  {
    return \cloudflow\api_helper::call('utils.xml.read_url', array('file_url' => $file_url));
  }
}

namespace cloudflow\utils\xml
{
  function write($json)
  {
    return \cloudflow\api_helper::call('utils.xml.write', array('json' => $json));
  }
}

namespace cloudflow\utils\http
{
  function post($host, $port, $url, $ssl, $data)
  {
    return \cloudflow\api_helper::call('utils.http.post', array('host' => $host, 'port' => $port, 'url' => $url, 'ssl' => $ssl, 'data' => $data));
  }
}

namespace cloudflow\utils\http
{
  function post_data($host, $port, $url, $ssl, $content_type, $data)
  {
    return \cloudflow\api_helper::call('utils.http.post_data', array('host' => $host, 'port' => $port, 'url' => $url, 'ssl' => $ssl, 'content_type' => $content_type, 'data' => $data));
  }
}

namespace cloudflow\utils
{
  function format_date($secs_since_epoch, $format, $time_zone_offset)
  {
    return \cloudflow\api_helper::call('utils.format_date', array('secs_since_epoch' => $secs_since_epoch, 'format' => $format, 'time_zone_offset' => $time_zone_offset));
  }
}

namespace cloudflow\utils
{
  function uuid()
  {
    return \cloudflow\api_helper::call('utils.uuid', array());
  }
}

namespace cloudflow\utils\csv
{
  function read($csv, $column_sep, $row_sep, $use_headers)
  {
    return \cloudflow\api_helper::call('utils.csv.read', array('csv' => $csv, 'column_sep' => $column_sep, 'row_sep' => $row_sep, 'use_headers' => $use_headers));
  }
}

namespace cloudflow\utils\csv
{
  function read_url($file_url, $column_sep, $row_sep, $use_headers)
  {
    return \cloudflow\api_helper::call('utils.csv.read_url', array('file_url' => $file_url, 'column_sep' => $column_sep, 'row_sep' => $row_sep, 'use_headers' => $use_headers));
  }
}

namespace cloudflow\utils\sql
{
  function query($db_url, $query, $params)
  {
    return \cloudflow\api_helper::call('utils.sql.query', array('db_url' => $db_url, 'query' => $query, 'params' => $params));
  }
}

namespace cloudflow\utils\sql
{
  function update($db_url, $queries)
  {
    return \cloudflow\api_helper::call('utils.sql.update', array('db_url' => $db_url, 'queries' => $queries));
  }
}

namespace cloudflow\utils
{
  function hash($data, $hash_method, $encoding)
  {
    return \cloudflow\api_helper::call('utils.hash', array('data' => $data, 'hash_method' => $hash_method, 'encoding' => $encoding));
  }
}

namespace cloudflow\eventhandlers
{
}

namespace cloudflow\eventhandlers
{
  function remove($eventhandler_id)
  {
    return \cloudflow\api_helper::call('eventhandlers.remove', array('eventhandler_id' => $eventhandler_id));
  }
}

namespace cloudflow\eventhandlers
{
  function list_all()
  {
    return \cloudflow\api_helper::call('eventhandlers.list_all', array());
  }
}

namespace cloudflow\eventhandlers
{
  function update($eventhandler_id, $name, $script, $scope, $trigger)
  {
    return \cloudflow\api_helper::call('eventhandlers.update', array('eventhandler_id' => $eventhandler_id, 'name' => $name, 'script' => $script, 'scope' => $scope, 'trigger' => $trigger));
  }
}

namespace cloudflow\eventhandlers
{
  function add($name, $script, $scope, $trigger)
  {
    return \cloudflow\api_helper::call('eventhandlers.add', array('name' => $name, 'script' => $script, 'scope' => $scope, 'trigger' => $trigger));
  }
}

namespace cloudflow\archive
{
}

namespace cloudflow\archive
{
  function unzip_files($archive, $folder, $options)
  {
    return \cloudflow\api_helper::call('archive.unzip_files', array('archive' => $archive, 'folder' => $folder, 'options' => $options));
  }
}

namespace cloudflow\archive
{
  function zip_files($archive_folder, $archive_name, $files_to_add, $options)
  {
    return \cloudflow\api_helper::call('archive.zip_files', array('archive_folder' => $archive_folder, 'archive_name' => $archive_name, 'files_to_add' => $files_to_add, 'options' => $options));
  }
}

namespace cloudflow\asset
{
}

namespace cloudflow\asset
{
  function create($data)
  {
    return \cloudflow\api_helper::call('asset.create', array('data' => $data));
  }
}

namespace cloudflow\asset
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('asset.delete', array('id' => $id));
  }
}

namespace cloudflow\asset
{
  function get($id)
  {
    return \cloudflow\api_helper::call('asset.get', array('id' => $id));
  }
}

namespace cloudflow\asset
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('asset.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\asset
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('asset.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\asset
{
  function remove_keys($id, $keys)
  {
    return \cloudflow\api_helper::call('asset.remove_keys', array('id' => $id, 'keys' => $keys));
  }
}

namespace cloudflow\asset
{
  function remove_keys_by_query($query, $keys, $options)
  {
    return \cloudflow\api_helper::call('asset.remove_keys_by_query', array('query' => $query, 'keys' => $keys, 'options' => $options));
  }
}

namespace cloudflow\asset
{
  function set_keys($id, $key_data)
  {
    return \cloudflow\api_helper::call('asset.set_keys', array('id' => $id, 'key_data' => $key_data));
  }
}

namespace cloudflow\asset
{
  function set_keys_by_query($query, $key_data, $options)
  {
    return \cloudflow\api_helper::call('asset.set_keys_by_query', array('query' => $query, 'key_data' => $key_data, 'options' => $options));
  }
}

namespace cloudflow\asset
{
  function update($data)
  {
    return \cloudflow\api_helper::call('asset.update', array('data' => $data));
  }
}

namespace cloudflow\asset
{
  function add_tag($asset, $tag)
  {
    return \cloudflow\api_helper::call('asset.add_tag', array('asset' => $asset, 'tag' => $tag));
  }
}

namespace cloudflow\asset
{
  function add_to_database($url)
  {
    return \cloudflow\api_helper::call('asset.add_to_database', array('url' => $url));
  }
}

namespace cloudflow\asset
{
  function download($url)
  {
    return \cloudflow\api_helper::call('asset.download', array('url' => $url));
  }
}

namespace cloudflow\asset
{
  function export_xfdf($asset_url, $xfdf_url)
  {
    return \cloudflow\api_helper::call('asset.export_xfdf', array('asset_url' => $asset_url, 'xfdf_url' => $xfdf_url));
  }
}

namespace cloudflow\asset
{
  function get_by_url($url)
  {
    return \cloudflow\api_helper::call('asset.get_by_url', array('url' => $url));
  }
}

namespace cloudflow\asset
{
  function get_notes($asset)
  {
    return \cloudflow\api_helper::call('asset.get_notes', array('asset' => $asset));
  }
}

namespace cloudflow\asset
{
  function get_tags($asset)
  {
    return \cloudflow\api_helper::call('asset.get_tags', array('asset' => $asset));
  }
}

namespace cloudflow\asset
{
  function has_all_tags($asset, $tags)
  {
    return \cloudflow\api_helper::call('asset.has_all_tags', array('asset' => $asset, 'tags' => $tags));
  }
}

namespace cloudflow\asset
{
  function has_at_least_one_tag($asset, $tags)
  {
    return \cloudflow\api_helper::call('asset.has_at_least_one_tag', array('asset' => $asset, 'tags' => $tags));
  }
}

namespace cloudflow\asset
{
  function has_tag($asset, $tag)
  {
    return \cloudflow\api_helper::call('asset.has_tag', array('asset' => $asset, 'tag' => $tag));
  }
}

namespace cloudflow\asset
{
  function list_with_tag_query($query, $tag_query, $tag_mode, $fields)
  {
    return \cloudflow\api_helper::call('asset.list_with_tag_query', array('query' => $query, 'tag_query' => $tag_query, 'tag_mode' => $tag_mode, 'fields' => $fields));
  }
}

namespace cloudflow\asset
{
  function list_with_tag_query_and_options($query, $tag_query, $tag_mode, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('asset.list_with_tag_query_and_options', array('query' => $query, 'tag_query' => $tag_query, 'tag_mode' => $tag_mode, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\asset
{
  function remove_tag($asset, $tag)
  {
    return \cloudflow\api_helper::call('asset.remove_tag', array('asset' => $asset, 'tag' => $tag));
  }
}

namespace cloudflow\asset
{
  function set_priority($asset, $priority)
  {
    return \cloudflow\api_helper::call('asset.set_priority', array('asset' => $asset, 'priority' => $priority));
  }
}

namespace cloudflow\asset
{
  function import_xfdf($asset_url, $xfdf_url, $user_id, $options)
  {
    return \cloudflow\api_helper::call('asset.import_xfdf', array('asset_url' => $asset_url, 'xfdf_url' => $xfdf_url, 'user_id' => $user_id, 'options' => $options));
  }
}

namespace cloudflow\curve
{
}

namespace cloudflow\curve
{
  function interpolate($curve_json)
  {
    return \cloudflow\api_helper::call('curve.interpolate', array('curve_json' => $curve_json));
  }
}

namespace cloudflow\curve
{
  function read_file($url)
  {
    return \cloudflow\api_helper::call('curve.read_file', array('url' => $url));
  }
}

namespace cloudflow\curve
{
  function write_file($data, $url, $options)
  {
    return \cloudflow\api_helper::call('curve.write_file', array('data' => $data, 'url' => $url, 'options' => $options));
  }
}

namespace cloudflow\custom_objects
{
}

namespace cloudflow\custom_objects
{
  function create($collection, $data)
  {
    return \cloudflow\api_helper::call('custom_objects.create', array('collection' => $collection, 'data' => $data));
  }
}

namespace cloudflow\custom_objects
{
  function delete($collection, $id)
  {
    return \cloudflow\api_helper::call('custom_objects.delete', array('collection' => $collection, 'id' => $id));
  }
}

namespace cloudflow\custom_objects
{
  function get($collection, $id)
  {
    return \cloudflow\api_helper::call('custom_objects.get', array('collection' => $collection, 'id' => $id));
  }
}

namespace cloudflow\custom_objects
{
  function list_all($collection, $query, $fields)
  {
    return \cloudflow\api_helper::call('custom_objects.list', array('collection' => $collection, 'query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\custom_objects
{
  function list_with_options($collection, $query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('custom_objects.list_with_options', array('collection' => $collection, 'query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\custom_objects
{
  function remove_keys($collection, $id, $keys)
  {
    return \cloudflow\api_helper::call('custom_objects.remove_keys', array('collection' => $collection, 'id' => $id, 'keys' => $keys));
  }
}

namespace cloudflow\custom_objects
{
  function set_keys($collection, $id, $key_data)
  {
    return \cloudflow\api_helper::call('custom_objects.set_keys', array('collection' => $collection, 'id' => $id, 'key_data' => $key_data));
  }
}

namespace cloudflow\custom_objects
{
  function set_scope($collection, $id, $scope_id)
  {
    return \cloudflow\api_helper::call('custom_objects.set_scope', array('collection' => $collection, 'id' => $id, 'scope_id' => $scope_id));
  }
}

namespace cloudflow\custom_objects
{
  function update($collection, $data)
  {
    return \cloudflow\api_helper::call('custom_objects.update', array('collection' => $collection, 'data' => $data));
  }
}

namespace cloudflow\email
{
}

namespace cloudflow\email
{
  function send_mail($email_address, $subject, $body)
  {
    return \cloudflow\api_helper::call('email.send_mail', array('email_address' => $email_address, 'subject' => $subject, 'body' => $body));
  }
}

namespace cloudflow\email
{
  function send_mail_with_attachements($email_address, $subject, $body, $attachments)
  {
    return \cloudflow\api_helper::call('email.send_mail_with_attachements', array('email_address' => $email_address, 'subject' => $subject, 'body' => $body, 'attachments' => $attachments));
  }
}

namespace cloudflow\file
{
}

namespace cloudflow\file
{
  function copy_file_with_options($from_file, $to_file_or_folder, $options)
  {
    return \cloudflow\api_helper::call('file.copy_file_with_options', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function copy_file($from_file, $to_file_or_folder)
  {
    return \cloudflow\api_helper::call('file.copy_file', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder));
  }
}

namespace cloudflow\file
{
  function copy_file_and_overwrite($from_file, $to_file_or_folder, $overwrite)
  {
    return \cloudflow\api_helper::call('file.copy_file_and_overwrite', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\file
{
  function copy_file_with_unique_name($from_file, $to_file_or_folder, $unique_name_mode)
  {
    return \cloudflow\api_helper::call('file.copy_file_with_unique_name', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder, 'unique_name_mode' => $unique_name_mode));
  }
}

namespace cloudflow\file
{
  function copy_folder($from_folder, $to_folder, $options)
  {
    return \cloudflow\api_helper::call('file.copy_folder', array('from_folder' => $from_folder, 'to_folder' => $to_folder, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function create_folder_with_options($inside_folder, $folder_to_create, $options)
  {
    return \cloudflow\api_helper::call('file.create_folder_with_options', array('inside_folder' => $inside_folder, 'folder_to_create' => $folder_to_create, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function create_folder($inside_folder, $folder_to_create)
  {
    return \cloudflow\api_helper::call('file.create_folder', array('inside_folder' => $inside_folder, 'folder_to_create' => $folder_to_create));
  }
}

namespace cloudflow\file
{
  function delete_file_with_options($file, $options)
  {
    return \cloudflow\api_helper::call('file.delete_file_with_options', array('file' => $file, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function delete_file($file)
  {
    return \cloudflow\api_helper::call('file.delete_file', array('file' => $file));
  }
}

namespace cloudflow\file
{
  function delete_folder_with_options($folder, $options)
  {
    return \cloudflow\api_helper::call('file.delete_folder_with_options', array('folder' => $folder, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function delete_folder($folder, $contents_only)
  {
    return \cloudflow\api_helper::call('file.delete_folder', array('folder' => $folder, 'contents_only' => $contents_only));
  }
}

namespace cloudflow\file
{
  function does_exist($file_or_folder)
  {
    return \cloudflow\api_helper::call('file.does_exist', array('file_or_folder' => $file_or_folder));
  }
}

namespace cloudflow\file
{
  function get_file_info($file)
  {
    return \cloudflow\api_helper::call('file.get_file_info', array('file' => $file));
  }
}

namespace cloudflow\file
{
  function get_file_info_from_file($file)
  {
    return \cloudflow\api_helper::call('file.get_file_info_from_file', array('file' => $file));
  }
}

namespace cloudflow\file
{
  function get_predefined_folder($folder_type)
  {
    return \cloudflow\api_helper::call('file.get_predefined_folder', array('folder_type' => $folder_type));
  }
}

namespace cloudflow\file
{
  function list_folder($folder, $options)
  {
    return \cloudflow\api_helper::call('file.list_folder', array('folder' => $folder, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function move_file_with_options($from_file, $to_file_or_folder, $options)
  {
    return \cloudflow\api_helper::call('file.move_file_with_options', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function move_file($from_file, $to_file_or_folder)
  {
    return \cloudflow\api_helper::call('file.move_file', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder));
  }
}

namespace cloudflow\file
{
  function move_file_and_overwrite($from_file, $to_file_or_folder, $overwrite)
  {
    return \cloudflow\api_helper::call('file.move_file_and_overwrite', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\file
{
  function move_file_with_unique_name($from_file, $to_file_or_folder, $unique_name_mode)
  {
    return \cloudflow\api_helper::call('file.move_file_with_unique_name', array('from_file' => $from_file, 'to_file_or_folder' => $to_file_or_folder, 'unique_name_mode' => $unique_name_mode));
  }
}

namespace cloudflow\file
{
  function move_folder($from_folder, $to_folder, $options)
  {
    return \cloudflow\api_helper::call('file.move_folder', array('from_folder' => $from_folder, 'to_folder' => $to_folder, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function move_file_to_trash($url, $options)
  {
    return \cloudflow\api_helper::call('file.move_file_to_trash', array('url' => $url, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function ospath_to_url($ospath, $base_url)
  {
    return \cloudflow\api_helper::call('file.ospath_to_url', array('ospath' => $ospath, 'base_url' => $base_url));
  }
}

namespace cloudflow\file
{
  function read_json_file($url)
  {
    return \cloudflow\api_helper::call('file.read_json_file', array('url' => $url));
  }
}

namespace cloudflow\file
{
  function rewrite_ospath($ospath, $from_notation, $to_notation)
  {
    return \cloudflow\api_helper::call('file.rewrite_ospath', array('ospath' => $ospath, 'from_notation' => $from_notation, 'to_notation' => $to_notation));
  }
}

namespace cloudflow\file
{
  function set_file_info($file, $file_info)
  {
    return \cloudflow\api_helper::call('file.set_file_info', array('file' => $file, 'file_info' => $file_info));
  }
}

namespace cloudflow\file
{
  function url_to_ospath($url)
  {
    return \cloudflow\api_helper::call('file.url_to_ospath', array('url' => $url));
  }
}

namespace cloudflow\file
{
  function write_json_file($data, $url, $options)
  {
    return \cloudflow\api_helper::call('file.write_json_file', array('data' => $data, 'url' => $url, 'options' => $options));
  }
}

namespace cloudflow\file
{
  function write_string($url, $string)
  {
    return \cloudflow\api_helper::call('file.write_string', array('url' => $url, 'string' => $string));
  }
}

namespace cloudflow\file
{
  function read_string($url)
  {
    return \cloudflow\api_helper::call('file.read_string', array('url' => $url));
  }
}

namespace cloudflow\file
{
  function fileExists($url)
  {
    return \cloudflow\api_helper::call('file.fileExists', array('url' => $url));
  }
}

namespace cloudflow\file
{
  function read_json_from_url($url)
  {
    return \cloudflow\api_helper::call('file.read_json_from_url', array('url' => $url));
  }
}

namespace cloudflow\folder
{
}

namespace cloudflow\folder
{
  function get($id)
  {
    return \cloudflow\api_helper::call('folder.get', array('id' => $id));
  }
}

namespace cloudflow\folder
{
  function add_tag($folder, $tag)
  {
    return \cloudflow\api_helper::call('folder.add_tag', array('folder' => $folder, 'tag' => $tag));
  }
}

namespace cloudflow\folder
{
  function get_changes($url, $filter, $client_time, $timestamp)
  {
    return \cloudflow\api_helper::call('folder.get_changes', array('url' => $url, 'filter' => $filter, 'client_time' => $client_time, 'timestamp' => $timestamp));
  }
}

namespace cloudflow\folder
{
  function get_tags($folder)
  {
    return \cloudflow\api_helper::call('folder.get_tags', array('folder' => $folder));
  }
}

namespace cloudflow\folder
{
  function has_all_tags($folder, $tags)
  {
    return \cloudflow\api_helper::call('folder.has_all_tags', array('folder' => $folder, 'tags' => $tags));
  }
}

namespace cloudflow\folder
{
  function has_at_least_one_tag($folder, $tags)
  {
    return \cloudflow\api_helper::call('folder.has_at_least_one_tag', array('folder' => $folder, 'tags' => $tags));
  }
}

namespace cloudflow\folder
{
  function has_tag($folder, $tag)
  {
    return \cloudflow\api_helper::call('folder.has_tag', array('folder' => $folder, 'tag' => $tag));
  }
}

namespace cloudflow\folder
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('folder.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\folder
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('folder.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\folder
{
  function list_with_tag_query($query, $tag_query, $tag_mode, $fields)
  {
    return \cloudflow\api_helper::call('folder.list_with_tag_query', array('query' => $query, 'tag_query' => $tag_query, 'tag_mode' => $tag_mode, 'fields' => $fields));
  }
}

namespace cloudflow\folder
{
  function list_with_tag_query_and_options($query, $tag_query, $tag_mode, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('folder.list_with_tag_query_and_options', array('query' => $query, 'tag_query' => $tag_query, 'tag_mode' => $tag_mode, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\folder
{
  function remove_tag($folder, $tag)
  {
    return \cloudflow\api_helper::call('folder.remove_tag', array('folder' => $folder, 'tag' => $tag));
  }
}

namespace cloudflow\form
{
}

namespace cloudflow\form
{
  function create($data)
  {
    return \cloudflow\api_helper::call('form.create', array('data' => $data));
  }
}

namespace cloudflow\form
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('form.delete', array('id' => $id));
  }
}

namespace cloudflow\form
{
  function get($id)
  {
    return \cloudflow\api_helper::call('form.get', array('id' => $id));
  }
}

namespace cloudflow\form
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('form.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\form
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('form.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\form
{
  function remove_keys($id, $keys)
  {
    return \cloudflow\api_helper::call('form.remove_keys', array('id' => $id, 'keys' => $keys));
  }
}

namespace cloudflow\form
{
  function remove_keys_by_query($query, $keys, $options)
  {
    return \cloudflow\api_helper::call('form.remove_keys_by_query', array('query' => $query, 'keys' => $keys, 'options' => $options));
  }
}

namespace cloudflow\form
{
  function set_keys($id, $key_data)
  {
    return \cloudflow\api_helper::call('form.set_keys', array('id' => $id, 'key_data' => $key_data));
  }
}

namespace cloudflow\form
{
  function set_keys_by_query($query, $key_data, $options)
  {
    return \cloudflow\api_helper::call('form.set_keys_by_query', array('query' => $query, 'key_data' => $key_data, 'options' => $options));
  }
}

namespace cloudflow\form
{
  function update($data)
  {
    return \cloudflow\api_helper::call('form.update', array('data' => $data));
  }
}

namespace cloudflow\form
{
  function upload($contents)
  {
    return \cloudflow\api_helper::call('form.upload', array('contents' => $contents));
  }
}

namespace cloudflow\media
{
}

namespace cloudflow\media
{
  function create($data)
  {
    return \cloudflow\api_helper::call('media.create', array('data' => $data));
  }
}

namespace cloudflow\media
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('media.delete', array('id' => $id));
  }
}

namespace cloudflow\media
{
  function get($id)
  {
    return \cloudflow\api_helper::call('media.get', array('id' => $id));
  }
}

namespace cloudflow\media
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('media.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\media
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('media.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\media
{
  function update($data)
  {
    return \cloudflow\api_helper::call('media.update', array('data' => $data));
  }
}

namespace cloudflow\network
{
}

namespace cloudflow\network
{
  function download_to_file($url, $file)
  {
    return \cloudflow\api_helper::call('network.download_to_file', array('url' => $url, 'file' => $file));
  }
}

namespace cloudflow\network
{
  function upload_file($file, $url)
  {
    return \cloudflow\api_helper::call('network.upload_file', array('file' => $file, 'url' => $url));
  }
}

namespace cloudflow\network
{
  function upload_file_with_authentication($file, $url, $username, $password)
  {
    return \cloudflow\api_helper::call('network.upload_file_with_authentication', array('file' => $file, 'url' => $url, 'username' => $username, 'password' => $password));
  }
}

namespace cloudflow\network
{
  function call_rest($http_method, $url, $content_type, $request_data)
  {
    return \cloudflow\api_helper::call('network.call_rest', array('http_method' => $http_method, 'url' => $url, 'content_type' => $content_type, 'request_data' => $request_data));
  }
}

namespace cloudflow\network
{
  function call_rest_with_options($http_method, $url, $content_type, $request_data, $options)
  {
    return \cloudflow\api_helper::call('network.call_rest_with_options', array('http_method' => $http_method, 'url' => $url, 'content_type' => $content_type, 'request_data' => $request_data, 'options' => $options));
  }
}

namespace cloudflow\network
{
  function call_soap($url, $name, $parameters)
  {
    return \cloudflow\api_helper::call('network.call_soap', array('url' => $url, 'name' => $name, 'parameters' => $parameters));
  }
}

namespace cloudflow\network
{
  function call_soap_with_local_wsdl($url, $name, $parameters, $wsdl_url)
  {
    return \cloudflow\api_helper::call('network.call_soap_with_local_wsdl', array('url' => $url, 'name' => $name, 'parameters' => $parameters, 'wsdl_url' => $wsdl_url));
  }
}

namespace cloudflow\network
{
  function call_soap_with_options($url, $name, $parameters, $options)
  {
    return \cloudflow\api_helper::call('network.call_soap_with_options', array('url' => $url, 'name' => $name, 'parameters' => $parameters, 'options' => $options));
  }
}

namespace cloudflow\output_device
{
}

namespace cloudflow\output_device
{
  function create($data)
  {
    return \cloudflow\api_helper::call('output_device.create', array('data' => $data));
  }
}

namespace cloudflow\output_device
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('output_device.delete', array('id' => $id));
  }
}

namespace cloudflow\output_device
{
  function get($id)
  {
    return \cloudflow\api_helper::call('output_device.get', array('id' => $id));
  }
}

namespace cloudflow\output_device
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('output_device.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\output_device
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('output_device.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\output_device
{
  function update($data)
  {
    return \cloudflow\api_helper::call('output_device.update', array('data' => $data));
  }
}

namespace cloudflow\pdf
{
}

namespace cloudflow\pdf
{
  function pdf_validate($url)
  {
    return \cloudflow\api_helper::call('pdf.pdf_validate', array('url' => $url));
  }
}

namespace cloudflow\pdf
{
  function join_pages($files, $target_folder, $options)
  {
    return \cloudflow\api_helper::call('pdf.join_pages', array('files' => $files, 'target_folder' => $target_folder, 'options' => $options));
  }
}

namespace cloudflow\pdf
{
  function split_pages($url, $target_folder, $options)
  {
    return \cloudflow\api_helper::call('pdf.split_pages', array('url' => $url, 'target_folder' => $target_folder, 'options' => $options));
  }
}

namespace cloudflow\pdf
{
  function save_rasterized_to_file($input_url, $rasterized_url, $options)
  {
    return \cloudflow\api_helper::call('pdf.save_rasterized_to_file', array('input_url' => $input_url, 'rasterized_url' => $rasterized_url, 'options' => $options));
  }
}

namespace cloudflow\preferences
{
}

namespace cloudflow\preferences
{
  function get_for_current_user($application_id, $sub_key)
  {
    return \cloudflow\api_helper::call('preferences.get_for_current_user', array('application_id' => $application_id, 'sub_key' => $sub_key));
  }
}

namespace cloudflow\preferences
{
  function get_for_realm($realm_type, $realm_value, $application_id, $sub_key)
  {
    return \cloudflow\api_helper::call('preferences.get_for_realm', array('realm_type' => $realm_type, 'realm_value' => $realm_value, 'application_id' => $application_id, 'sub_key' => $sub_key));
  }
}

namespace cloudflow\preferences
{
  function save_for_current_user($preferences, $application_id, $sub_key)
  {
    return \cloudflow\api_helper::call('preferences.save_for_current_user', array('preferences' => $preferences, 'application_id' => $application_id, 'sub_key' => $sub_key));
  }
}

namespace cloudflow\preferences
{
  function save_for_realm($preferences, $realm_type, $realm_value, $application_id, $sub_key)
  {
    return \cloudflow\api_helper::call('preferences.save_for_realm', array('preferences' => $preferences, 'realm_type' => $realm_type, 'realm_value' => $realm_value, 'application_id' => $application_id, 'sub_key' => $sub_key));
  }
}

namespace cloudflow\printer
{
}

namespace cloudflow\printer
{
  function do_print($printer_name, $file, $options)
  {
    return \cloudflow\api_helper::call('printer.print', array('printer_name' => $printer_name, 'file' => $file, 'options' => $options));
  }
}

namespace cloudflow\resource
{
}

namespace cloudflow\resource
{
  function download($url)
  {
    return \cloudflow\api_helper::call('resource.download', array('url' => $url));
  }
}

namespace cloudflow\resource
{
  function enumerate($type)
  {
    return \cloudflow\api_helper::call('resource.enumerate', array('type' => $type));
  }
}

namespace cloudflow\rss
{
}

namespace cloudflow\rss
{
  function get_pending_approvals_url()
  {
    return \cloudflow\api_helper::call('rss.get_pending_approvals_url', array());
  }
}

namespace cloudflow\subProcess
{
}

namespace cloudflow\subProcess
{
  function call_with_options($command, $arguments, $options)
  {
    return \cloudflow\api_helper::call('subProcess.call_with_options', array('command' => $command, 'arguments' => $arguments, 'options' => $options));
  }
}

namespace cloudflow\subProcess
{
  function call($command, $arguments, $osList, $hostList)
  {
    return \cloudflow\api_helper::call('subProcess.call', array('command' => $command, 'arguments' => $arguments, 'osList' => $osList, 'hostList' => $hostList));
  }
}

namespace cloudflow\tag
{
}

namespace cloudflow\tag
{
  function create($data)
  {
    return \cloudflow\api_helper::call('tag.create', array('data' => $data));
  }
}

namespace cloudflow\tag
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('tag.delete', array('id' => $id));
  }
}

namespace cloudflow\tag
{
  function get($id)
  {
    return \cloudflow\api_helper::call('tag.get', array('id' => $id));
  }
}

namespace cloudflow\tag
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('tag.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\tag
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('tag.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\tag
{
  function get_all_names()
  {
    return \cloudflow\api_helper::call('tag.get_all_names', array());
  }
}

namespace cloudflow\users
{
}

namespace cloudflow\users
{
  function create($data)
  {
    return \cloudflow\api_helper::call('users.create', array('data' => $data));
  }
}

namespace cloudflow\users
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('users.delete', array('id' => $id));
  }
}

namespace cloudflow\users
{
  function get($id)
  {
    return \cloudflow\api_helper::call('users.get', array('id' => $id));
  }
}

namespace cloudflow\users
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('users.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\users
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('users.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\users
{
  function update($data)
  {
    return \cloudflow\api_helper::call('users.update', array('data' => $data));
  }
}

namespace cloudflow\users
{
  function get_by_user_name($username)
  {
    return \cloudflow\api_helper::call('users.get_by_user_name', array('username' => $username));
  }
}

namespace cloudflow\users
{
  function get_by_email($email)
  {
    return \cloudflow\api_helper::call('users.get_by_email', array('email' => $email));
  }
}

namespace cloudflow\users
{
  function get_contact_by_email($email)
  {
    return \cloudflow\api_helper::call('users.get_contact_by_email', array('email' => $email));
  }
}

namespace cloudflow\users
{
  function get_permissions($user_id)
  {
    return \cloudflow\api_helper::call('users.get_permissions', array('user_id' => $user_id));
  }
}

namespace cloudflow\users
{
  function is_admin($user_id)
  {
    return \cloudflow\api_helper::call('users.is_admin', array('user_id' => $user_id));
  }
}

namespace cloudflow\users
{
  function add_permission($user_id, $permissions)
  {
    return \cloudflow\api_helper::call('users.add_permission', array('user_id' => $user_id, 'permissions' => $permissions));
  }
}

namespace cloudflow\users
{
  function remove_permission($user_id, $permissions)
  {
    return \cloudflow\api_helper::call('users.remove_permission', array('user_id' => $user_id, 'permissions' => $permissions));
  }
}

namespace cloudflow\users
{
  function get_all_permissions()
  {
    return \cloudflow\api_helper::call('users.get_all_permissions', array());
  }
}

namespace cloudflow\users
{
  function add_contact($email, $fullname, $scope, $attributes)
  {
    return \cloudflow\api_helper::call('users.add_contact', array('email' => $email, 'fullname' => $fullname, 'scope' => $scope, 'attributes' => $attributes));
  }
}

namespace cloudflow\users
{
  function add_user($username, $userpass, $fullname, $email, $scope)
  {
    return \cloudflow\api_helper::call('users.add_user', array('username' => $username, 'userpass' => $userpass, 'fullname' => $fullname, 'email' => $email, 'scope' => $scope));
  }
}

namespace cloudflow\users
{
  function get_contact_by_id($contact_id)
  {
    return \cloudflow\api_helper::call('users.get_contact_by_id', array('contact_id' => $contact_id));
  }
}

namespace cloudflow\users
{
  function get_user_by_username($username)
  {
    return \cloudflow\api_helper::call('users.get_user_by_username', array('username' => $username));
  }
}

namespace cloudflow\users
{
  function list_contacts()
  {
    return \cloudflow\api_helper::call('users.list_contacts', array());
  }
}

namespace cloudflow\users
{
  function update_contact($contact_id, $email, $fullname, $scope, $attributes)
  {
    return \cloudflow\api_helper::call('users.update_contact', array('contact_id' => $contact_id, 'email' => $email, 'fullname' => $fullname, 'scope' => $scope, 'attributes' => $attributes));
  }
}

namespace cloudflow\users
{
  function remove_contact($contact_id)
  {
    return \cloudflow\api_helper::call('users.remove_contact', array('contact_id' => $contact_id));
  }
}

namespace cloudflow\users
{
  function list_users()
  {
    return \cloudflow\api_helper::call('users.list_users', array());
  }
}

namespace cloudflow\users
{
  function update_user($user_id, $username, $userpass, $fullname, $email, $scope, $add_permissions, $remove_permissions)
  {
    return \cloudflow\api_helper::call('users.update_user', array('user_id' => $user_id, 'username' => $username, 'userpass' => $userpass, 'fullname' => $fullname, 'email' => $email, 'scope' => $scope, 'add_permissions' => $add_permissions, 'remove_permissions' => $remove_permissions));
  }
}

namespace cloudflow\users
{
  function remove_user($user_id)
  {
    return \cloudflow\api_helper::call('users.remove_user', array('user_id' => $user_id));
  }
}

namespace cloudflow\users
{
  function get_user_id($username)
  {
    return \cloudflow\api_helper::call('users.get_user_id', array('username' => $username));
  }
}

namespace cloudflow\webtrace
{
}

namespace cloudflow\webtrace
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('webtrace.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\webtrace
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('webtrace.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\registration
{
}

namespace cloudflow\registration
{
  function create($data)
  {
    return \cloudflow\api_helper::call('registration.create', array('data' => $data));
  }
}

namespace cloudflow\registration
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('registration.delete', array('id' => $id));
  }
}

namespace cloudflow\registration
{
  function get($id)
  {
    return \cloudflow\api_helper::call('registration.get', array('id' => $id));
  }
}

namespace cloudflow\registration
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('registration.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\registration
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('registration.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\registration
{
  function update($data)
  {
    return \cloudflow\api_helper::call('registration.update', array('data' => $data));
  }
}

namespace cloudflow\registration
{
  function send_email($_id)
  {
    return \cloudflow\api_helper::call('registration.send_email', array('_id' => $_id));
  }
}

namespace cloudflow\license
{
}

namespace cloudflow\license
{
  function download_new($customer_code, $serial)
  {
    return \cloudflow\api_helper::call('license.download_new', array('customer_code' => $customer_code, 'serial' => $serial));
  }
}

namespace cloudflow\license
{
  function get()
  {
    return \cloudflow\api_helper::call('license.get', array());
  }
}

namespace cloudflow\license
{
  function get_system_id()
  {
    return \cloudflow\api_helper::call('license.get_system_id', array());
  }
}

namespace cloudflow\license
{
  function install($customer_code, $serial, $license)
  {
    return \cloudflow\api_helper::call('license.install', array('customer_code' => $customer_code, 'serial' => $serial, 'license' => $license));
  }
}

namespace cloudflow\license
{
  function reset()
  {
    return \cloudflow\api_helper::call('license.reset', array());
  }
}

namespace cloudflow\license
{
  function update()
  {
    return \cloudflow\api_helper::call('license.update', array());
  }
}

namespace cloudflow\metadata
{
}

namespace cloudflow\metadata
{
  function get_from_asset_with_options($file, $options)
  {
    return \cloudflow\api_helper::call('metadata.get_from_asset_with_options', array('file' => $file, 'options' => $options));
  }
}

namespace cloudflow\metadata
{
  function get_from_asset($file)
  {
    return \cloudflow\api_helper::call('metadata.get_from_asset', array('file' => $file));
  }
}

namespace cloudflow\metadata
{
  function get_from_file_with_options($file, $options)
  {
    return \cloudflow\api_helper::call('metadata.get_from_file_with_options', array('file' => $file, 'options' => $options));
  }
}

namespace cloudflow\metadata
{
  function get_from_file($file)
  {
    return \cloudflow\api_helper::call('metadata.get_from_file', array('file' => $file));
  }
}

namespace cloudflow\metadata
{
  function get_metadata($file)
  {
    return \cloudflow\api_helper::call('metadata.get_metadata', array('file' => $file));
  }
}

namespace cloudflow\metadata
{
  function get_preview($file, $page, $size)
  {
    return \cloudflow\api_helper::call('metadata.get_preview', array('file' => $file, 'page' => $page, 'size' => $size));
  }
}

namespace cloudflow\metadata
{
  function get_preview_with_options($file, $options)
  {
    return \cloudflow\api_helper::call('metadata.get_preview_with_options', array('file' => $file, 'options' => $options));
  }
}

namespace cloudflow\metadata
{
  function get_thumbnail($file, $page)
  {
    return \cloudflow\api_helper::call('metadata.get_thumbnail', array('file' => $file, 'page' => $page));
  }
}

namespace cloudflow\metadata
{
  function save_preview_to_file($url, $page, $preview_url, $document_type, $size, $overwrite)
  {
    return \cloudflow\api_helper::call('metadata.save_preview_to_file', array('url' => $url, 'page' => $page, 'preview_url' => $preview_url, 'document_type' => $document_type, 'size' => $size, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\metadata
{
  function save_preview_to_file_with_options($url, $preview_url, $options)
  {
    return \cloudflow\api_helper::call('metadata.save_preview_to_file_with_options', array('url' => $url, 'preview_url' => $preview_url, 'options' => $options));
  }
}

namespace cloudflow\metadata
{
  function save_thumbnail_to_file($url, $page, $thumbnail_url, $document_type, $overwrite)
  {
    return \cloudflow\api_helper::call('metadata.save_thumbnail_to_file', array('url' => $url, 'page' => $page, 'thumbnail_url' => $thumbnail_url, 'document_type' => $document_type, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\metadata
{
  function set_in_asset($file, $meta_data)
  {
    return \cloudflow\api_helper::call('metadata.set_in_asset', array('file' => $file, 'meta_data' => $meta_data));
  }
}

namespace cloudflow\proofscope
{
}

namespace cloudflow\proofscope
{
  function complete_view($view, $options)
  {
    return \cloudflow\api_helper::call('proofscope.complete_view', array('view' => $view, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_file_url($host_url, $approval_id, $participant_email)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_file_url', array('host_url' => $host_url, 'approval_id' => $approval_id, 'participant_email' => $participant_email));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_file_url_for_pending_approval($host_url, $file_url, $participant_email)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_file_url_for_pending_approval', array('host_url' => $host_url, 'file_url' => $file_url, 'participant_email' => $participant_email));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_file_url_with_options($host_url, $approval_id, $participant_email, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_file_url_with_options', array('host_url' => $host_url, 'approval_id' => $approval_id, 'participant_email' => $participant_email, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_file_url_for_pending_approval_with_options($host_url, $file_url, $participant_email, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_file_url_for_pending_approval_with_options', array('host_url' => $host_url, 'file_url' => $file_url, 'participant_email' => $participant_email, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_folder_as_versions_url($host_url, $folder_url, $approval_id, $participant_email)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_folder_as_versions_url', array('host_url' => $host_url, 'folder_url' => $folder_url, 'approval_id' => $approval_id, 'participant_email' => $participant_email));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_folder_as_versions_url_for_pending_approval($host_url, $folder_url, $participant_email)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_folder_as_versions_url_for_pending_approval', array('host_url' => $host_url, 'folder_url' => $folder_url, 'participant_email' => $participant_email));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_folder_as_versions_url_with_options($host_url, $folder_url, $approval_id, $participant_email, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_folder_as_versions_url_with_options', array('host_url' => $host_url, 'folder_url' => $folder_url, 'approval_id' => $approval_id, 'participant_email' => $participant_email, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_folder_as_versions_url_for_pending_approval_with_options($host_url, $folder_url, $participant_email, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_folder_as_versions_url_for_pending_approval_with_options', array('host_url' => $host_url, 'folder_url' => $folder_url, 'participant_email' => $participant_email, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_versions_url($host_url, $file_urls, $approval_id, $participant_email)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_versions_url', array('host_url' => $host_url, 'file_urls' => $file_urls, 'approval_id' => $approval_id, 'participant_email' => $participant_email));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_versions_url_for_pending_approval($host_url, $file_urls, $participant_email)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_versions_url_for_pending_approval', array('host_url' => $host_url, 'file_urls' => $file_urls, 'participant_email' => $participant_email));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_versions_url_with_options($host_url, $file_urls, $approval_id, $participant_email, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_versions_url_with_options', array('host_url' => $host_url, 'file_urls' => $file_urls, 'approval_id' => $approval_id, 'participant_email' => $participant_email, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_approve_versions_url_for_pending_approval_with_options($host_url, $file_urls, $participant_email, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_approve_versions_url_for_pending_approval_with_options', array('host_url' => $host_url, 'file_urls' => $file_urls, 'participant_email' => $participant_email, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_view_file_difference_url($host_url, $file_url, $diff_url)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_file_difference_url', array('host_url' => $host_url, 'file_url' => $file_url, 'diff_url' => $diff_url));
  }
}

namespace cloudflow\proofscope
{
  function create_view_file_difference_url_with_options($host_url, $file_url, $diff_url, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_file_difference_url_with_options', array('host_url' => $host_url, 'file_url' => $file_url, 'diff_url' => $diff_url, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_view_file_url($host_url, $file_url)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_file_url', array('host_url' => $host_url, 'file_url' => $file_url));
  }
}

namespace cloudflow\proofscope
{
  function create_view_file_url_with_options($host_url, $file_url, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_file_url_with_options', array('host_url' => $host_url, 'file_url' => $file_url, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_view_folder_as_versions_url($host_url, $folder_url)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_folder_as_versions_url', array('host_url' => $host_url, 'folder_url' => $folder_url));
  }
}

namespace cloudflow\proofscope
{
  function create_view_folder_as_versions_url_with_options($host_url, $folder_url, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_folder_as_versions_url_with_options', array('host_url' => $host_url, 'folder_url' => $folder_url, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function create_view_versions_url($host_url, $file_urls)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_versions_url', array('host_url' => $host_url, 'file_urls' => $file_urls));
  }
}

namespace cloudflow\proofscope
{
  function create_view_versions_url_with_options($host_url, $file_urls, $options)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_versions_url_with_options', array('host_url' => $host_url, 'file_urls' => $file_urls, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function get_3d_controller_data($asset_id)
  {
    return \cloudflow\api_helper::call('proofscope.get_3d_controller_data', array('asset_id' => $asset_id));
  }
}

namespace cloudflow\proofscope
{
  function get_3d_render_status($asset_id)
  {
    return \cloudflow\api_helper::call('proofscope.get_3d_render_status', array('asset_id' => $asset_id));
  }
}

namespace cloudflow\proofscope
{
  function get_generate_text_layer_status_by_view($view)
  {
    return \cloudflow\api_helper::call('proofscope.get_generate_text_layer_status_by_view', array('view' => $view));
  }
}

namespace cloudflow\proofscope
{
  function get_graphic_barcode($view, $a_x, $a_y, $b_x, $b_y, $options)
  {
    return \cloudflow\api_helper::call('proofscope.get_graphic_barcode', array('view' => $view, 'a_x' => $a_x, 'a_y' => $a_y, 'b_x' => $b_x, 'b_y' => $b_y, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function get_graphic_halftone($view, $a_x, $a_y, $b_x, $b_y, $options)
  {
    return \cloudflow\api_helper::call('proofscope.get_graphic_halftone', array('view' => $view, 'a_x' => $a_x, 'a_y' => $a_y, 'b_x' => $b_x, 'b_y' => $b_y, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function get_graphic_sample($view, $x, $y, $options)
  {
    return \cloudflow\api_helper::call('proofscope.get_graphic_sample', array('view' => $view, 'x' => $x, 'y' => $y, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function get_graphic_text_layer($view, $options)
  {
    return \cloudflow\api_helper::call('proofscope.get_graphic_text_layer', array('view' => $view, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function get_render_status_by_view($view)
  {
    return \cloudflow\api_helper::call('proofscope.get_render_status_by_view', array('view' => $view));
  }
}

namespace cloudflow\proofscope
{
  function get_view_info($query)
  {
    return \cloudflow\api_helper::call('proofscope.get_view_info', array('query' => $query));
  }
}

namespace cloudflow\proofscope
{
  function request_render_by_url($url)
  {
    return \cloudflow\api_helper::call('proofscope.request_render_by_url', array('url' => $url));
  }
}

namespace cloudflow\proofscope
{
  function render($asset_url)
  {
    return \cloudflow\api_helper::call('proofscope.render', array('asset_url' => $asset_url));
  }
}

namespace cloudflow\proofscope
{
  function request_3d_render($asset_id)
  {
    return \cloudflow\api_helper::call('proofscope.request_3d_render', array('asset_id' => $asset_id));
  }
}

namespace cloudflow\proofscope
{
  function request_3d_render_by_url($url)
  {
    return \cloudflow\api_helper::call('proofscope.request_3d_render_by_url', array('url' => $url));
  }
}

namespace cloudflow\proofscope
{
  function request_text_layer_generation_by_view($view)
  {
    return \cloudflow\api_helper::call('proofscope.request_text_layer_generation_by_view', array('view' => $view));
  }
}

namespace cloudflow\proofscope
{
  function request_text_layer_generation_by_url($url)
  {
    return \cloudflow\api_helper::call('proofscope.request_text_layer_generation_by_url', array('url' => $url));
  }
}

namespace cloudflow\proofscope
{
  function request_rerender_by_url($url)
  {
    return \cloudflow\api_helper::call('proofscope.request_rerender_by_url', array('url' => $url));
  }
}

namespace cloudflow\proofscope
{
  function request_render_by_view($view)
  {
    return \cloudflow\api_helper::call('proofscope.request_render_by_view', array('view' => $view));
  }
}

namespace cloudflow\proofscope
{
  function save_rasterized_view_to_file($view, $rasterized_url, $options)
  {
    return \cloudflow\api_helper::call('proofscope.save_rasterized_view_to_file', array('view' => $view, 'rasterized_url' => $rasterized_url, 'options' => $options));
  }
}

namespace cloudflow\proofscope
{
  function save_view($view, $asset_id)
  {
    return \cloudflow\api_helper::call('proofscope.save_view', array('view' => $view, 'asset_id' => $asset_id));
  }
}

namespace cloudflow\proofscope
{
  function login_with_guest_token($token, $params)
  {
    return \cloudflow\api_helper::call('proofscope.login_with_guest_token', array('token' => $token, 'params' => $params));
  }
}

namespace cloudflow\proofscope
{
  function create_view_url($host_url, $file_url, $permissions)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_url', array('host_url' => $host_url, 'file_url' => $file_url, 'permissions' => $permissions));
  }
}

namespace cloudflow\proofscope
{
  function create_diff_url($host_url, $file_url, $fileB_url, $permissions)
  {
    return \cloudflow\api_helper::call('proofscope.create_diff_url', array('host_url' => $host_url, 'file_url' => $file_url, 'fileB_url' => $fileB_url, 'permissions' => $permissions));
  }
}

namespace cloudflow\proofscope
{
  function create_view_folder_url($host_url, $folder_url, $email, $permissions)
  {
    return \cloudflow\api_helper::call('proofscope.create_view_folder_url', array('host_url' => $host_url, 'folder_url' => $folder_url, 'email' => $email, 'permissions' => $permissions));
  }
}

namespace cloudflow\proofscope
{
  function create_url($host, $asset_url, $permissions, $expiry, $username, $diff_url)
  {
    return \cloudflow\api_helper::call('proofscope.create_url', array('host' => $host, 'asset_url' => $asset_url, 'permissions' => $permissions, 'expiry' => $expiry, 'username' => $username, 'diff_url' => $diff_url));
  }
}

namespace cloudflow\proofscope
{
  function create_url_with_parameters($host_url, $username, $expiry, $parameters)
  {
    return \cloudflow\api_helper::call('proofscope.create_url_with_parameters', array('host_url' => $host_url, 'username' => $username, 'expiry' => $expiry, 'parameters' => $parameters));
  }
}

namespace cloudflow\proofscope
{
  function invite($asset_url, $email_addresses, $email_message, $host, $permissions, $expiry)
  {
    return \cloudflow\api_helper::call('proofscope.invite', array('asset_url' => $asset_url, 'email_addresses' => $email_addresses, 'email_message' => $email_message, 'host' => $host, 'permissions' => $permissions, 'expiry' => $expiry));
  }
}

namespace cloudflow\notes
{
}

namespace cloudflow\notes
{
  function get_by_asset_id($asset_id, $comment_format)
  {
    return \cloudflow\api_helper::call('notes.get_by_asset_id', array('asset_id' => $asset_id, 'comment_format' => $comment_format));
  }
}

namespace cloudflow\notes
{
  function get_overview_by_reference($reference, $only_comments, $comment_format)
  {
    return \cloudflow\api_helper::call('notes.get_overview_by_reference', array('reference' => $reference, 'only_comments' => $only_comments, 'comment_format' => $comment_format));
  }
}

namespace cloudflow\ic3d
{
}

namespace cloudflow\ic3d
{
  function render_labels($url, $options)
  {
    return \cloudflow\api_helper::call('ic3d.render_labels', array('url' => $url, 'options' => $options));
  }
}

namespace cloudflow\ic3d
{
  function render_view_to_file($url, $view, $view_url, $document_type, $size, $overwrite)
  {
    return \cloudflow\api_helper::call('ic3d.render_view_to_file', array('url' => $url, 'view' => $view, 'view_url' => $view_url, 'document_type' => $document_type, 'size' => $size, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\ic3d
{
  function replace_labels($url, $replacements, $options)
  {
    return \cloudflow\api_helper::call('ic3d.replace_labels', array('url' => $url, 'replacements' => $replacements, 'options' => $options));
  }
}

namespace cloudflow\software_update
{
}

namespace cloudflow\software_update
{
  function create($data)
  {
    return \cloudflow\api_helper::call('software_update.create', array('data' => $data));
  }
}

namespace cloudflow\software_update
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('software_update.delete', array('id' => $id));
  }
}

namespace cloudflow\software_update
{
  function get($id)
  {
    return \cloudflow\api_helper::call('software_update.get', array('id' => $id));
  }
}

namespace cloudflow\software_update
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('software_update.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\software_update
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('software_update.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\software_update
{
  function check_for_updates($do_download)
  {
    return \cloudflow\api_helper::call('software_update.check_for_updates', array('do_download' => $do_download));
  }
}

namespace cloudflow\software_update
{
  function download_archive($update_id)
  {
    return \cloudflow\api_helper::call('software_update.download_archive', array('update_id' => $update_id));
  }
}

namespace cloudflow\software_update
{
  function list_updates()
  {
    return \cloudflow\api_helper::call('software_update.list_updates', array());
  }
}

namespace cloudflow\bluecollardefinition
{
}

namespace cloudflow\bluecollardefinition
{
  function create($data)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.create', array('data' => $data));
  }
}

namespace cloudflow\bluecollardefinition
{
  function download($id)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.download', array('id' => $id));
  }
}

namespace cloudflow\bluecollardefinition
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.delete', array('id' => $id));
  }
}

namespace cloudflow\bluecollardefinition
{
  function get($id)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.get', array('id' => $id));
  }
}

namespace cloudflow\bluecollardefinition
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\bluecollardefinition
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\bluecollardefinition
{
  function update($data)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.update', array('data' => $data));
  }
}

namespace cloudflow\bluecollardefinition
{
  function upload($contents)
  {
    return \cloudflow\api_helper::call('bluecollardefinition.upload', array('contents' => $contents));
  }
}

namespace cloudflow\hub
{
}

namespace cloudflow\hub
{
  function abort_jacket($jacket_id, $immediate_kill)
  {
    return \cloudflow\api_helper::call('hub.abort_jacket', array('jacket_id' => $jacket_id, 'immediate_kill' => $immediate_kill));
  }
}

namespace cloudflow\hub
{
  function abort_workable($workable_id, $immediate_kill)
  {
    return \cloudflow\api_helper::call('hub.abort_workable', array('workable_id' => $workable_id, 'immediate_kill' => $immediate_kill));
  }
}

namespace cloudflow\hub
{
  function change_workable_priority($workable_id, $priority)
  {
    return \cloudflow\api_helper::call('hub.change_workable_priority', array('workable_id' => $workable_id, 'priority' => $priority));
  }
}

namespace cloudflow\hub
{
  function check_waiting_room_of_workable($workable_id, $collar, $node_id, $connector)
  {
    return \cloudflow\api_helper::call('hub.check_waiting_room_of_workable', array('workable_id' => $workable_id, 'collar' => $collar, 'node_id' => $node_id, 'connector' => $connector));
  }
}

namespace cloudflow\hub
{
  function continue_workable($workable_id, $node_name, $to_connector)
  {
    return \cloudflow\api_helper::call('hub.continue_workable', array('workable_id' => $workable_id, 'node_name' => $node_name, 'to_connector' => $to_connector));
  }
}

namespace cloudflow\hub
{
  function continue_workable_with_variables($workable_id, $node_name, $to_connector, $variables)
  {
    return \cloudflow\api_helper::call('hub.continue_workable_with_variables', array('workable_id' => $workable_id, 'node_name' => $node_name, 'to_connector' => $to_connector, 'variables' => $variables));
  }
}

namespace cloudflow\hub
{
  function continue_workable_from_kiosk($workable_id, $node_id, $to_connector, $files, $variables)
  {
    return \cloudflow\api_helper::call('hub.continue_workable_from_kiosk', array('workable_id' => $workable_id, 'node_id' => $node_id, 'to_connector' => $to_connector, 'files' => $files, 'variables' => $variables));
  }
}

namespace cloudflow\hub
{
  function cleanup_jacket($jacket_id, $whitepaper_id)
  {
    return \cloudflow\api_helper::call('hub.cleanup_jacket', array('jacket_id' => $jacket_id, 'whitepaper_id' => $whitepaper_id));
  }
}

namespace cloudflow\hub
{
  function create_jacket($jacket_name)
  {
    return \cloudflow\api_helper::call('hub.create_jacket', array('jacket_name' => $jacket_name));
  }
}

namespace cloudflow\hub
{
  function create_jacket_with_variables($jacket_name, $variables)
  {
    return \cloudflow\api_helper::call('hub.create_jacket_with_variables', array('jacket_name' => $jacket_name, 'variables' => $variables));
  }
}

namespace cloudflow\hub
{
  function get_jacket_actions($jacket_id)
  {
    return \cloudflow\api_helper::call('hub.get_jacket_actions', array('jacket_id' => $jacket_id));
  }
}

namespace cloudflow\hub
{
  function get_overview($query, $previous_timestamp)
  {
    return \cloudflow\api_helper::call('hub.get_overview', array('query' => $query, 'previous_timestamp' => $previous_timestamp));
  }
}

namespace cloudflow\hub
{
  function get_variables_from_jacket($jacket_id, $variables)
  {
    return \cloudflow\api_helper::call('hub.get_variables_from_jacket', array('jacket_id' => $jacket_id, 'variables' => $variables));
  }
}

namespace cloudflow\hub
{
  function get_variables_from_workable($workable_id, $variables)
  {
    return \cloudflow\api_helper::call('hub.get_variables_from_workable', array('workable_id' => $workable_id, 'variables' => $variables));
  }
}

namespace cloudflow\hub
{
  function get_waiting_room_of_workable($workable_id)
  {
    return \cloudflow\api_helper::call('hub.get_waiting_room_of_workable', array('workable_id' => $workable_id));
  }
}

namespace cloudflow\hub
{
  function get_whitepaper_input_options($whitepaper_name, $input_name)
  {
    return \cloudflow\api_helper::call('hub.get_whitepaper_input_options', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name));
  }
}

namespace cloudflow\hub
{
  function get_whitepaper_input_options_data($whitepaper_name, $input_name, $data, $files)
  {
    return \cloudflow\api_helper::call('hub.get_whitepaper_input_options_data', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'data' => $data, 'files' => $files));
  }
}

namespace cloudflow\hub
{
  function get_whitepaper_inputs($options)
  {
    return \cloudflow\api_helper::call('hub.get_whitepaper_inputs', array('options' => $options));
  }
}

namespace cloudflow\hub
{
  function get_workable_hold_options($workable_id, $node_id)
  {
    return \cloudflow\api_helper::call('hub.get_workable_hold_options', array('workable_id' => $workable_id, 'node_id' => $node_id));
  }
}

namespace cloudflow\hub
{
  function get_workable_hold_options_data($workable_id, $node_id, $data)
  {
    return \cloudflow\api_helper::call('hub.get_workable_hold_options_data', array('workable_id' => $workable_id, 'node_id' => $node_id, 'data' => $data));
  }
}

namespace cloudflow\hub
{
  function move_uploaded_files($urls, $whitepaper_name, $input_name)
  {
    return \cloudflow\api_helper::call('hub.move_uploaded_files', array('urls' => $urls, 'whitepaper_name' => $whitepaper_name, 'input_name' => $input_name));
  }
}

namespace cloudflow\hub
{
  function poll_for_workable_result($workable_id, $time_out)
  {
    return \cloudflow\api_helper::call('hub.poll_for_workable_result', array('workable_id' => $workable_id, 'time_out' => $time_out));
  }
}

namespace cloudflow\hub
{
  function prepare_jacket_rerun($whitepaper_name, $input_name, $jacket_id, $workable_id)
  {
    return \cloudflow\api_helper::call('hub.prepare_jacket_rerun', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'jacket_id' => $jacket_id, 'workable_id' => $workable_id));
  }
}

namespace cloudflow\hub
{
  function process_from_whitepaper($whitepaper_name, $input_name, $time_out)
  {
    return \cloudflow\api_helper::call('hub.process_from_whitepaper', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'time_out' => $time_out));
  }
}

namespace cloudflow\hub
{
  function process_from_whitepaper_with_variables($whitepaper_name, $input_name, $variables, $time_out)
  {
    return \cloudflow\api_helper::call('hub.process_from_whitepaper_with_variables', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'variables' => $variables, 'time_out' => $time_out));
  }
}

namespace cloudflow\hub
{
  function process_from_whitepaper_with_files_and_variables($whitepaper_name, $input_name, $files, $variables, $time_out)
  {
    return \cloudflow\api_helper::call('hub.process_from_whitepaper_with_files_and_variables', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'files' => $files, 'variables' => $variables, 'time_out' => $time_out));
  }
}

namespace cloudflow\hub
{
  function start_from_whitepaper($whitepaper_name, $input_name)
  {
    return \cloudflow\api_helper::call('hub.start_from_whitepaper', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name));
  }
}

namespace cloudflow\hub
{
  function start_from_whitepaper_with_variables($whitepaper_name, $input_name, $variables)
  {
    return \cloudflow\api_helper::call('hub.start_from_whitepaper_with_variables', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'variables' => $variables));
  }
}

namespace cloudflow\hub
{
  function start_from_whitepaper_with_files_and_variables($whitepaper_name, $input_name, $files, $variables)
  {
    return \cloudflow\api_helper::call('hub.start_from_whitepaper_with_files_and_variables', array('whitepaper_name' => $whitepaper_name, 'input_name' => $input_name, 'files' => $files, 'variables' => $variables));
  }
}

namespace cloudflow\jacket
{
}

namespace cloudflow\jacket
{
  function create($data)
  {
    return \cloudflow\api_helper::call('jacket.create', array('data' => $data));
  }
}

namespace cloudflow\jacket
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('jacket.delete', array('id' => $id));
  }
}

namespace cloudflow\jacket
{
  function download($id)
  {
    return \cloudflow\api_helper::call('jacket.download', array('id' => $id));
  }
}

namespace cloudflow\jacket
{
  function get($id)
  {
    return \cloudflow\api_helper::call('jacket.get', array('id' => $id));
  }
}

namespace cloudflow\jacket
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('jacket.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\jacket
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('jacket.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\jacket
{
  function update($data)
  {
    return \cloudflow\api_helper::call('jacket.update', array('data' => $data));
  }
}

namespace cloudflow\valuecomposer
{
}

namespace cloudflow\valuecomposer
{
  function create($data)
  {
    return \cloudflow\api_helper::call('valuecomposer.create', array('data' => $data));
  }
}

namespace cloudflow\valuecomposer
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('valuecomposer.delete', array('id' => $id));
  }
}

namespace cloudflow\valuecomposer
{
  function download($id)
  {
    return \cloudflow\api_helper::call('valuecomposer.download', array('id' => $id));
  }
}

namespace cloudflow\valuecomposer
{
  function get($id)
  {
    return \cloudflow\api_helper::call('valuecomposer.get', array('id' => $id));
  }
}

namespace cloudflow\valuecomposer
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('valuecomposer.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\valuecomposer
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('valuecomposer.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\valuecomposer
{
  function update($data)
  {
    return \cloudflow\api_helper::call('valuecomposer.update', array('data' => $data));
  }
}

namespace cloudflow\whitepaper
{
}

namespace cloudflow\whitepaper
{
  function backup($query, $options)
  {
    return \cloudflow\api_helper::call('whitepaper.backup', array('query' => $query, 'options' => $options));
  }
}

namespace cloudflow\whitepaper
{
  function create($data)
  {
    return \cloudflow\api_helper::call('whitepaper.create', array('data' => $data));
  }
}

namespace cloudflow\whitepaper
{
  function get($id)
  {
    return \cloudflow\api_helper::call('whitepaper.get', array('id' => $id));
  }
}

namespace cloudflow\whitepaper
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('whitepaper.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\whitepaper
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('whitepaper.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\whitepaper
{
  function restore($backup, $options)
  {
    return \cloudflow\api_helper::call('whitepaper.restore', array('backup' => $backup, 'options' => $options));
  }
}

namespace cloudflow\whitepaper
{
  function create_from_template($template_id)
  {
    return \cloudflow\api_helper::call('whitepaper.create_from_template', array('template_id' => $template_id));
  }
}

namespace cloudflow\whitepaper
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('whitepaper.delete', array('id' => $id));
  }
}

namespace cloudflow\whitepaper
{
  function download($id)
  {
    return \cloudflow\api_helper::call('whitepaper.download', array('id' => $id));
  }
}

namespace cloudflow\whitepaper
{
  function evaluate_value_composer($composers)
  {
    return \cloudflow\api_helper::call('whitepaper.evaluate_value_composer', array('composers' => $composers));
  }
}

namespace cloudflow\whitepaper
{
  function get_sub_flow_parameters($whitepaper_name, $sub_flow_name)
  {
    return \cloudflow\api_helper::call('whitepaper.get_sub_flow_parameters', array('whitepaper_name' => $whitepaper_name, 'sub_flow_name' => $sub_flow_name));
  }
}

namespace cloudflow\whitepaper
{
  function get_template_parameters($id, $template_id)
  {
    return \cloudflow\api_helper::call('whitepaper.get_template_parameters', array('id' => $id, 'template_id' => $template_id));
  }
}

namespace cloudflow\whitepaper
{
  function get_value_composer_editor_configuration($context)
  {
    return \cloudflow\api_helper::call('whitepaper.get_value_composer_editor_configuration', array('context' => $context));
  }
}

namespace cloudflow\whitepaper
{
  function update($data)
  {
    return \cloudflow\api_helper::call('whitepaper.update', array('data' => $data));
  }
}

namespace cloudflow\whitepaper
{
  function update_template_parameters($id, $parameters, $template_id)
  {
    return \cloudflow\api_helper::call('whitepaper.update_template_parameters', array('id' => $id, 'parameters' => $parameters, 'template_id' => $template_id));
  }
}

namespace cloudflow\whitepaper
{
  function upload($contents)
  {
    return \cloudflow\api_helper::call('whitepaper.upload', array('contents' => $contents));
  }
}

namespace cloudflow\workable
{
}

namespace cloudflow\workable
{
  function create($data)
  {
    return \cloudflow\api_helper::call('workable.create', array('data' => $data));
  }
}

namespace cloudflow\workable
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('workable.delete', array('id' => $id));
  }
}

namespace cloudflow\workable
{
  function download($id)
  {
    return \cloudflow\api_helper::call('workable.download', array('id' => $id));
  }
}

namespace cloudflow\workable
{
  function get($id)
  {
    return \cloudflow\api_helper::call('workable.get', array('id' => $id));
  }
}

namespace cloudflow\workable
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('workable.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\workable
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('workable.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\workable
{
  function update($data)
  {
    return \cloudflow\api_helper::call('workable.update', array('data' => $data));
  }
}

namespace cloudflow\workable
{
  function abort($id, $immediate_kill)
  {
    return \cloudflow\api_helper::call('workable.abort', array('id' => $id, 'immediate_kill' => $immediate_kill));
  }
}

namespace cloudflow\workable
{
  function get_executed_parameters_for_node($id, $node)
  {
    return \cloudflow\api_helper::call('workable.get_executed_parameters_for_node', array('id' => $id, 'node' => $node));
  }
}

namespace cloudflow\workable
{
  function get_merged_variables($id)
  {
    return \cloudflow\api_helper::call('workable.get_merged_variables', array('id' => $id));
  }
}

namespace cloudflow\workable
{
  function get_messages($id, $severity)
  {
    return \cloudflow\api_helper::call('workable.get_messages', array('id' => $id, 'severity' => $severity));
  }
}

namespace cloudflow\workable
{
  function get_messages_for_node($id, $node, $severity)
  {
    return \cloudflow\api_helper::call('workable.get_messages_for_node', array('id' => $id, 'node' => $node, 'severity' => $severity));
  }
}

namespace cloudflow\workable
{
  function get_original_parameters_for_node($id, $node)
  {
    return \cloudflow\api_helper::call('workable.get_original_parameters_for_node', array('id' => $id, 'node' => $node));
  }
}

namespace cloudflow\workable
{
  function get_output_for_node($id, $node)
  {
    return \cloudflow\api_helper::call('workable.get_output_for_node', array('id' => $id, 'node' => $node));
  }
}

namespace cloudflow\workable
{
  function get_progress($id)
  {
    return \cloudflow\api_helper::call('workable.get_progress', array('id' => $id));
  }
}

namespace cloudflow\workable
{
  function get_resolved_parameters_for_node($id, $node)
  {
    return \cloudflow\api_helper::call('workable.get_resolved_parameters_for_node', array('id' => $id, 'node' => $node));
  }
}

namespace cloudflow\workable
{
  function move($id, $from_node_id, $from_connector, $to_node_id, $to_connector)
  {
    return \cloudflow\api_helper::call('workable.move', array('id' => $id, 'from_node_id' => $from_node_id, 'from_connector' => $from_connector, 'to_node_id' => $to_node_id, 'to_connector' => $to_connector));
  }
}

namespace cloudflow\approval
{
}

namespace cloudflow\approval
{
  function create($data)
  {
    return \cloudflow\api_helper::call('approval.create', array('data' => $data));
  }
}

namespace cloudflow\approval
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('approval.delete', array('id' => $id));
  }
}

namespace cloudflow\approval
{
  function get($id)
  {
    return \cloudflow\api_helper::call('approval.get', array('id' => $id));
  }
}

namespace cloudflow\approval
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('approval.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\approval
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('approval.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\approval
{
  function remove_keys($id, $keys)
  {
    return \cloudflow\api_helper::call('approval.remove_keys', array('id' => $id, 'keys' => $keys));
  }
}

namespace cloudflow\approval
{
  function remove_keys_by_query($query, $keys, $options)
  {
    return \cloudflow\api_helper::call('approval.remove_keys_by_query', array('query' => $query, 'keys' => $keys, 'options' => $options));
  }
}

namespace cloudflow\approval
{
  function set_keys($id, $key_data)
  {
    return \cloudflow\api_helper::call('approval.set_keys', array('id' => $id, 'key_data' => $key_data));
  }
}

namespace cloudflow\approval
{
  function set_keys_by_query($query, $key_data, $options)
  {
    return \cloudflow\api_helper::call('approval.set_keys_by_query', array('query' => $query, 'key_data' => $key_data, 'options' => $options));
  }
}

namespace cloudflow\approval
{
  function update($data)
  {
    return \cloudflow\api_helper::call('approval.update', array('data' => $data));
  }
}

namespace cloudflow\approval
{
  function assess($approval_id, $user_email, $assessment)
  {
    return \cloudflow\api_helper::call('approval.assess', array('approval_id' => $approval_id, 'user_email' => $user_email, 'assessment' => $assessment));
  }
}

namespace cloudflow\approval
{
  function assess_from_workable($workable_id, $assessment)
  {
    return \cloudflow\api_helper::call('approval.assess_from_workable', array('workable_id' => $workable_id, 'assessment' => $assessment));
  }
}

namespace cloudflow\approval
{
  function cancel($approval_id)
  {
    return \cloudflow\api_helper::call('approval.cancel', array('approval_id' => $approval_id));
  }
}

namespace cloudflow\approval
{
  function cancel_from_workable($workable_id)
  {
    return \cloudflow\api_helper::call('approval.cancel_from_workable', array('workable_id' => $workable_id));
  }
}

namespace cloudflow\approval
{
  function delegate($approval_id, $user_email, $delegate_emails, $mode)
  {
    return \cloudflow\api_helper::call('approval.delegate', array('approval_id' => $approval_id, 'user_email' => $user_email, 'delegate_emails' => $delegate_emails, 'mode' => $mode));
  }
}

namespace cloudflow\approval
{
  function delegate_from_workable($workable_id, $delegate_emails, $mode)
  {
    return \cloudflow\api_helper::call('approval.delegate_from_workable', array('workable_id' => $workable_id, 'delegate_emails' => $delegate_emails, 'mode' => $mode));
  }
}

namespace cloudflow\approval
{
  function force($approval_id, $assessment)
  {
    return \cloudflow\api_helper::call('approval.force', array('approval_id' => $approval_id, 'assessment' => $assessment));
  }
}

namespace cloudflow\approval
{
  function force_from_workable($workable_id, $assessment)
  {
    return \cloudflow\api_helper::call('approval.force_from_workable', array('workable_id' => $workable_id, 'assessment' => $assessment));
  }
}

namespace cloudflow\approval
{
  function get_assessment($approval_id)
  {
    return \cloudflow\api_helper::call('approval.get_assessment', array('approval_id' => $approval_id));
  }
}

namespace cloudflow\approval
{
  function get_overview_by_reference($reference, $flatten_iterations, $inline_sub_approvals)
  {
    return \cloudflow\api_helper::call('approval.get_overview_by_reference', array('reference' => $reference, 'flatten_iterations' => $flatten_iterations, 'inline_sub_approvals' => $inline_sub_approvals));
  }
}

namespace cloudflow\approval
{
  function get_participant_assessment($approval_id, $user_email)
  {
    return \cloudflow\api_helper::call('approval.get_participant_assessment', array('approval_id' => $approval_id, 'user_email' => $user_email));
  }
}

namespace cloudflow\approval
{
  function get_participant_assessment_by_url($url, $user_email)
  {
    return \cloudflow\api_helper::call('approval.get_participant_assessment_by_url', array('url' => $url, 'user_email' => $user_email));
  }
}

namespace cloudflow\approval
{
  function get_participant_assessment_by_asset_id($asset_id, $user_email)
  {
    return \cloudflow\api_helper::call('approval.get_participant_assessment_by_asset_id', array('asset_id' => $asset_id, 'user_email' => $user_email));
  }
}

namespace cloudflow\approval
{
  function remove_by_reference($reference)
  {
    return \cloudflow\api_helper::call('approval.remove_by_reference', array('reference' => $reference));
  }
}

namespace cloudflow\approval_whitepaper_proxy
{
}

namespace cloudflow\approval_whitepaper_proxy
{
  function add($data)
  {
    return \cloudflow\api_helper::call('approval_whitepaper_proxy.add', array('data' => $data));
  }
}

namespace cloudflow\approval_whitepaper_proxy
{
  function delete($id)
  {
    return \cloudflow\api_helper::call('approval_whitepaper_proxy.delete', array('id' => $id));
  }
}

namespace cloudflow\approval_whitepaper_proxy
{
  function get($id)
  {
    return \cloudflow\api_helper::call('approval_whitepaper_proxy.get', array('id' => $id));
  }
}

namespace cloudflow\approval_whitepaper_proxy
{
  function list_all()
  {
    return \cloudflow\api_helper::call('approval_whitepaper_proxy.list', array());
  }
}

namespace cloudflow\approval_whitepaper_proxy
{
  function update($id, $data)
  {
    return \cloudflow\api_helper::call('approval_whitepaper_proxy.update', array('id' => $id, 'data' => $data));
  }
}

namespace cloudflow\printplanner
{
}

namespace cloudflow\printplanner
{
  function create_pdf($layout, $output)
  {
    return \cloudflow\api_helper::call('printplanner.create_pdf', array('layout' => $layout, 'output' => $output));
  }
}

namespace cloudflow\printplanner
{
  function create_cheatsheet_pdf($layout, $output)
  {
    return \cloudflow\api_helper::call('printplanner.create_cheatsheet_pdf', array('layout' => $layout, 'output' => $output));
  }
}

namespace cloudflow\printplanner
{
  function create_mom($layout, $output, $job, $options, $export_patch_surfaces)
  {
    return \cloudflow\api_helper::call('printplanner.create_mom', array('layout' => $layout, 'output' => $output, 'job' => $job, 'options' => $options, 'export_patch_surfaces' => $export_patch_surfaces));
  }
}

namespace cloudflow\printplanner
{
  function bin_pack($layout, $h_margin, $v_margin, $exclude_patches, $gutter_margin, $start_bottom)
  {
    return \cloudflow\api_helper::call('printplanner.bin_pack', array('layout' => $layout, 'h_margin' => $h_margin, 'v_margin' => $v_margin, 'exclude_patches' => $exclude_patches, 'gutter_margin' => $gutter_margin, 'start_bottom' => $start_bottom));
  }
}

namespace cloudflow\printplanner
{
  function detect_patches($layout, $url, $horizontalmargin, $verticalmargin)
  {
    return \cloudflow\api_helper::call('printplanner.detect_patches', array('layout' => $layout, 'url' => $url, 'horizontalmargin' => $horizontalmargin, 'verticalmargin' => $verticalmargin));
  }
}

namespace cloudflow\printplanner
{
  function detect_zero_patches($url)
  {
    return \cloudflow\api_helper::call('printplanner.detect_zero_patches', array('url' => $url));
  }
}

namespace cloudflow\printplanner
{
  function layout_to_pdf($layout, $data, $pdf, $overwrite)
  {
    return \cloudflow\api_helper::call('printplanner.layout_to_pdf', array('layout' => $layout, 'data' => $data, 'pdf' => $pdf, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\printplanner
{
  function create_jdf($layout, $output, $flavour)
  {
    return \cloudflow\api_helper::call('printplanner.create_jdf', array('layout' => $layout, 'output' => $output, 'flavour' => $flavour));
  }
}

namespace cloudflow\printplanner
{
  function decorate_pdf($file, $decorate, $data, $decorated_file, $overwrite)
  {
    return \cloudflow\api_helper::call('printplanner.decorate_pdf', array('file' => $file, 'decorate' => $decorate, 'data' => $data, 'decorated_file' => $decorated_file, 'overwrite' => $overwrite));
  }
}

namespace cloudflow\printplanner
{
  function install_patchplanner($application_paths)
  {
    return \cloudflow\api_helper::call('printplanner.install_patchplanner', array('application_paths' => $application_paths));
  }
}

namespace cloudflow\printplanner
{
  function check_patchplanner_install($folders)
  {
    return \cloudflow\api_helper::call('printplanner.check_patchplanner_install', array('folders' => $folders));
  }
}

namespace cloudflow\printplanner
{
  function get_patch_placement_info($jobs_path, $sheets_path)
  {
    return \cloudflow\api_helper::call('printplanner.get_patch_placement_info', array('jobs_path' => $jobs_path, 'sheets_path' => $sheets_path));
  }
}

namespace cloudflow\printplanner
{
  function invalidate_cache($cloudflow_path)
  {
    return \cloudflow\api_helper::call('printplanner.invalidate_cache', array('cloudflow_path' => $cloudflow_path));
  }
}

namespace cloudflow\printplanner
{
  function mom_file_exists($mom_file, $jobs_path)
  {
    return \cloudflow\api_helper::call('printplanner.mom_file_exists', array('mom_file' => $mom_file, 'jobs_path' => $jobs_path));
  }
}

namespace cloudflow\printplanner
{
  function install_printplanner()
  {
    return \cloudflow\api_helper::call('printplanner.install_printplanner', array());
  }
}

namespace cloudflow\pipeline
{
}

namespace cloudflow\pipeline
{
  function call_mashup($host, $port, $ssl, $mashup, $data)
  {
    return \cloudflow\api_helper::call('pipeline.call_mashup', array('host' => $host, 'port' => $port, 'ssl' => $ssl, 'mashup' => $mashup, 'data' => $data));
  }
}

namespace cloudflow\estimation
{
}

namespace cloudflow\estimation
{
  function parceling($algorithm, $input, $options)
  {
    return \cloudflow\api_helper::call('estimation.parceling', array('algorithm' => $algorithm, 'input' => $input, 'options' => $options));
  }
}

namespace cloudflow\dataconnector
{
}

namespace cloudflow\dataconnector
{
  function custom_function($data_connector, $function, $parameters, $options)
  {
    return \cloudflow\api_helper::call('dataconnector.custom_function', array('data_connector' => $data_connector, 'function' => $function, 'parameters' => $parameters, 'options' => $options));
  }
}

namespace cloudflow\dataconnector
{
  function get($data_connector, $table, $record_id, $options)
  {
    return \cloudflow\api_helper::call('dataconnector.get', array('data_connector' => $data_connector, 'table' => $table, 'record_id' => $record_id, 'options' => $options));
  }
}

namespace cloudflow\dataconnector
{
  function list_all($data_connector, $table, $query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('dataconnector.list', array('data_connector' => $data_connector, 'table' => $table, 'query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\dataconnector
{
  function list_hql($data_connector, $table, $hql_query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('dataconnector.list_hql', array('data_connector' => $data_connector, 'table' => $table, 'hql_query' => $hql_query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\dataconnector
{
  function update($data_connector, $table, $record_data, $options)
  {
    return \cloudflow\api_helper::call('dataconnector.update', array('data_connector' => $data_connector, 'table' => $table, 'record_data' => $record_data, 'options' => $options));
  }
}

namespace cloudflow\xls
{
}

namespace cloudflow\xls
{
  function get_cell($file, $sheet, $cellname)
  {
    return \cloudflow\api_helper::call('xls.get_cell', array('file' => $file, 'sheet' => $sheet, 'cellname' => $cellname));
  }
}

namespace cloudflow\xls
{
  function to_json($excel_data, $options)
  {
    return \cloudflow\api_helper::call('xls.to_json', array('excel_data' => $excel_data, 'options' => $options));
  }
}

namespace cloudflow\xml
{
}

namespace cloudflow\xml
{
  function transform($xml_data, $transformation_url, $options)
  {
    return \cloudflow\api_helper::call('xml.transform', array('xml_data' => $xml_data, 'transformation_url' => $transformation_url, 'options' => $options));
  }
}

namespace cloudflow\xml
{
  function update($xml_data, $updates, $options)
  {
    return \cloudflow\api_helper::call('xml.update', array('xml_data' => $xml_data, 'updates' => $updates, 'options' => $options));
  }
}

namespace cloudflow\xml
{
  function xml_to_json($xml_data, $xml_template, $strip_whitespace)
  {
    return \cloudflow\api_helper::call('xml.xml_to_json', array('xml_data' => $xml_data, 'xml_template' => $xml_template, 'strip_whitespace' => $strip_whitespace));
  }
}

namespace cloudflow\len
{
}

namespace cloudflow\len
{
  function to_tiff($len, $tiff, $options)
  {
    return \cloudflow\api_helper::call('len.to_tiff', array('len' => $len, 'tiff' => $tiff, 'options' => $options));
  }
}

namespace cloudflow\share
{
}

namespace cloudflow\share
{
  function get_status()
  {
    return \cloudflow\api_helper::call('share.get_status', array());
  }
}

namespace cloudflow\site
{
}

namespace cloudflow\site
{
  function add($url, $admin_name, $admin_password)
  {
    return \cloudflow\api_helper::call('site.add', array('url' => $url, 'admin_name' => $admin_name, 'admin_password' => $admin_password));
  }
}

namespace cloudflow\site
{
  function finish_update_file($cloudflow_url, $md5, $md5_ad, $data_type, $apple_double, $mod_time, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.finish_update_file', array('cloudflow_url' => $cloudflow_url, 'md5' => $md5, 'md5_ad' => $md5_ad, 'data_type' => $data_type, 'apple_double' => $apple_double, 'mod_time' => $mod_time, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function forget_sync($sync_spec_name, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.forget_sync', array('sync_spec_name' => $sync_spec_name, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function init_sync($cloudflow_url, $md5, $md5_ad, $file_size, $block_size, $data_type, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.init_sync', array('cloudflow_url' => $cloudflow_url, 'md5' => $md5, 'md5_ad' => $md5_ad, 'file_size' => $file_size, 'block_size' => $block_size, 'data_type' => $data_type, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function list_all()
  {
    return \cloudflow\api_helper::call('site.list', array());
  }
}

namespace cloudflow\site
{
  function list_files($sync_spec_name, $time, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.list_files', array('sync_spec_name' => $sync_spec_name, 'time' => $time, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function prepare_update_file($cloudflow_url, $data_size, $reuse_blocks, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.prepare_update_file', array('cloudflow_url' => $cloudflow_url, 'data_size' => $data_size, 'reuse_blocks' => $reuse_blocks, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function register($sites, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.register', array('sites' => $sites, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function remove_file($cloudflow_url, $signature, $sender_site)
  {
    return \cloudflow\api_helper::call('site.remove_file', array('cloudflow_url' => $cloudflow_url, 'signature' => $signature, 'sender_site' => $sender_site));
  }
}

namespace cloudflow\site
{
  function request_session($site, $user_name, $options)
  {
    return \cloudflow\api_helper::call('site.request_session', array('site' => $site, 'user_name' => $user_name, 'options' => $options));
  }
}

namespace cloudflow\site
{
  function sync_file($source_site, $source_url, $target_site, $target_url)
  {
    return \cloudflow\api_helper::call('site.sync_file', array('source_site' => $source_site, 'source_url' => $source_url, 'target_site' => $target_site, 'target_url' => $target_url));
  }
}

namespace cloudflow\site
{
  function setup($site)
  {
    return \cloudflow\api_helper::call('site.setup', array('site' => $site));
  }
}

namespace cloudflow\syncspec
{
}

namespace cloudflow\syncspec
{
  function create($data)
  {
    return \cloudflow\api_helper::call('syncspec.create', array('data' => $data));
  }
}

namespace cloudflow\syncspec
{
  function list_all($query, $fields)
  {
    return \cloudflow\api_helper::call('syncspec.list', array('query' => $query, 'fields' => $fields));
  }
}

namespace cloudflow\syncspec
{
  function list_with_options($query, $order_by, $fields, $options)
  {
    return \cloudflow\api_helper::call('syncspec.list_with_options', array('query' => $query, 'order_by' => $order_by, 'fields' => $fields, 'options' => $options));
  }
}

namespace cloudflow\syncspec
{
  function get($id)
  {
    return \cloudflow\api_helper::call('syncspec.get', array('id' => $id));
  }
}

namespace cloudflow\syncspec
{
  function delete($spec)
  {
    return \cloudflow\api_helper::call('syncspec.delete', array('spec' => $spec));
  }
}

namespace cloudflow\syncspec
{
  function add_mapping($spec, $mapping)
  {
    return \cloudflow\api_helper::call('syncspec.add_mapping', array('spec' => $spec, 'mapping' => $mapping));
  }
}

namespace cloudflow\syncspec
{
  function remove_mapping($spec, $mapping)
  {
    return \cloudflow\api_helper::call('syncspec.remove_mapping', array('spec' => $spec, 'mapping' => $mapping));
  }
}

namespace cloudflow\syncspec
{
  function remove_file_mapping($spec, $source_site, $source_url)
  {
    return \cloudflow\api_helper::call('syncspec.remove_file_mapping', array('spec' => $spec, 'source_site' => $source_site, 'source_url' => $source_url));
  }
}

namespace cloudflow\syncspec
{
  function resync($spec)
  {
    return \cloudflow\api_helper::call('syncspec.resync', array('spec' => $spec));
  }
}

namespace cloudflow\syncspec
{
  function update($data)
  {
    return \cloudflow\api_helper::call('syncspec.update', array('data' => $data));
  }
}
