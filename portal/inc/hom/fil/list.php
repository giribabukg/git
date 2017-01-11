<?php
class CInc_Hom_Fil_List extends CHtm_List {

  public function __construct() {
    $this -> mTitle = lan('hom-fil');

    $lFolder = new CApp_Finder('app');
    $lBase = rtrim($lFolder -> getPath(MID), DS);

    $this -> mDir = new CHom_Fil_Tree();
    $this -> mDir -> addRoot($lBase, lan('hom-fil.root'));

    $this -> mFil = new CHom_Fil_Files($lBase);
  }

  protected function getCreateDirectoryButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.directory.create').'">';
    $lReturn.= '  <button type="button" id="button.directory.create" class="btn" data-toggle="modal" data-target="#dialog\\.directory\\.create">';
    $lReturn.= '    <img src="img/ico/16/folder_create.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getRenameDirectoryButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.directory.rename').'">';
    $lReturn.= '  <button type="button" id="button.directory.rename" class="btn" data-toggle="modal" data-target="#dialog\\.directory\\.rename" disabled>';
    $lReturn.= '    <img src="img/ico/16/folder_rename.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getRemoveDirectoryButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.directory.remove').'">';
    $lReturn.= '  <button type="button" id="button.directory.remove" class="btn" data-toggle="modal" data-target="#dialog\\.directory\\.remove" disabled>';
    $lReturn.= '    <img src="img/ico/16/folder_remove.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getUploadFileButton() {
    if (!CCor_Cfg::get('flink', FALSE)) {
      return $this -> getNonFlinkUploadButton();
    } elseif (CCor_Cfg::get('flink', FALSE)) {
      return $this -> getFlinkUploadButton();
    }
  }

  protected function getNonFlinkUploadButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.file.upload').'">';
    $lReturn.= '  <button type="button" id="button.nonflink.file.upload" class="btn" data-toggle="modal" data-target="#dialog\\.nonflink\\.file\\.upload">';
    $lReturn.= '    <img src="img/ico/16/file_upload.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getFlinkUploadButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.file.upload').'">';
    $lReturn.= '  <button type="button" id="button.flink.file.upload" class="btn" data-toggle="modal" data-target="#dialog\\.flink\\.file\\.upload">';
    $lReturn.= '    <img src="img/ico/16/file_upload.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getRenameFileButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.file.rename').'">';
    $lReturn.= '  <button type="button" id="button.file.rename" class="btn" data-toggle="modal" data-target="#dialog\\.file\\.rename" disabled>';
    $lReturn.= '    <img src="img/ico/16/file_rename.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getRemoveFileButton() {
    $lReturn = '<span data-toggle="tooltip" title="'.lan('lib.file.remove').'">';
    $lReturn.= '  <button type="button" id="button.file.remove" class="btn" data-toggle="modal" data-target="#dialog\\.file\\.remove" disabled>';
    $lReturn.= '    <img src="img/ico/16/file_remove.png" alt="">';
    $lReturn.= '  </button>';
    $lReturn.= '</span>';
    return $lReturn;
  }

  protected function getCont() {
    $lReturn = '';

    $lReturn.= '<div class="tbl">'.LF;

    // HEADER
    $lReturn.= '  <div class="cap">'.htm($this -> mTitle).'</div>'.LF;

    // BUTTON BAR
    $lReturn.= '  <div class="sub p2">'.LF;
    $lReturn.= $this -> getCreateDirectoryButton();
    $lReturn.= $this -> getRenameDirectoryButton();
    $lReturn.= $this -> getRemoveDirectoryButton();
    $lReturn.= $this -> getUploadFileButton();
    $lReturn.= $this -> getRenameFileButton();
    $lReturn.= $this -> getRemoveFileButton();
    $lReturn.= '  </div>'.LF;

    // CONTENT
    $lReturn.= '  <div>'.LF;
    $lReturn.= '    <table>'.LF;
    $lReturn.= '      <tr>'.LF;
    $lReturn.= '        <td class="td1 p0 vat" data-source="directories" id="directories">'.LF;
    $lReturn.= $this -> mDir -> getContent();
    $lReturn.= '        </td>'.LF;
    $lReturn.= '        <td class="td1 p0 vat" data-source="files" id="files">'.LF;
    $lReturn.= $this -> mFil -> getContent();
    $lReturn.= '        </td>'.LF;
    $lReturn.= '      </tr>'.LF;
    $lReturn.= '    </table>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // directory create
    $lReturn.= '<div id="dialog.directory.create" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.directory.create').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= '          <label id="directory.create.label">'.lan('lib.directory.name').'</label>';
    $lReturn.= '        </p>';
    $lReturn.= '        <p>';
    $lReturn.= '          <input type="text" id="directory.create" class="w100p">';
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" onclick="Flow.Files.createDirectory();">'.lan('lib.ok').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // directory rename
    $lReturn.= '<div id="dialog.directory.rename" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.directory.rename').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= '          <label for="directory.rename.label">'.lan('lib.directory.name').'</label>';
    $lReturn.= '        </p>';
    $lReturn.= '        <p>';
    $lReturn.= '          <input type="text" id="directory.rename" class="w100p">';
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" onclick="Flow.Files.renameDirectory();">'.lan('lib.ok').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // directory remove
    $lReturn.= '<div id="dialog.directory.remove" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.directory.remove').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= lan('lib.directory.confirm.remove');
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" onclick="Flow.Files.removeDirectory();">'.lan('lib.ok').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // non-Flink file upload
    $lReturn.= '<div id="dialog.nonflink.file.upload" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.file.upload').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= '          <label for="file.upload">'.lan('lib.file.name').'</label>';
    $lReturn.= '        </p>';
    $lReturn.= '        <p>';
    $lReturn.= '          <iframe name="iframe.nonflink.file.upload" style="display:none"></iframe>';
    $lReturn.= '          <form id="form.nonflink.file.upload" action="index.php" method="post" enctype="multipart/form-data" target="iframe.nonflink.file.upload">';
    $lReturn.= '            <input type="hidden" name="act" value="job-fil.nonflinkfileupload">';
    $lReturn.= '            <input type="hidden" name="relativedirectory">';
    $lReturn.= '            <input type="hidden" name="directoryname">';
    $lReturn.= '            <input type="file" name="file">';
    $lReturn.= '          </form>';
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" onclick="Flow.Files.uploadFileNonFlink();">'.lan('lib.upload').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // Flink file upload
    $lReturn.= '<div id="dialog.flink.file.upload" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.file.upload').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= '          <label for="file.upload">'.lan('lib.file.name').'</label>';
    $lReturn.= '        </p>';
    $lReturn.= '        <p>';
    $lReturn.= '          <input type="text" id="file.upload" class="w100p">';
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" onclick="Flow.Files.uploadFileFlink();">'.lan('lib.upload').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // file rename
    $lReturn.= '<div id="dialog.file.rename" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.file.rename').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= '          <label for="file.rename">'.lan('lib.file.name').'</label>';
    $lReturn.= '        </p>';
    $lReturn.= '        <p>';
    $lReturn.= '          <input type="text" id="file.rename" class="w100p">';
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" onclick="Flow.Files.renameFile();">'.lan('lib.ok').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    // file remove
    $lReturn.= '<div id="dialog.file.remove" class="modal" tabindex="-1" role="dialog">'.LF;
    $lReturn.= '  <div class="modal-dialog" role="document">'.LF;
    $lReturn.= '    <div class="modal-content">'.LF;
    $lReturn.= '      <div class="modal-header">'.LF;
    $lReturn.= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.LF;
    $lReturn.= '        <h4 class="modal-title">'.lan('lib.file.remove').'</h4>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-body">'.LF;
    $lReturn.= '        <p>';
    $lReturn.= lan('lib.file.confirm.remove');
    $lReturn.= '        </p>';
    $lReturn.= '      </div>'.LF;
    $lReturn.= '      <div class="modal-footer">'.LF;
    $lReturn.= '        <button type="button" class="btn btn-primary" id="okay" onclick="Flow.Files.removeFile();">'.lan('lib.ok').'</button>'.LF;
    $lReturn.= '        <button type="button" class="btn btn-default" id="cancel" data-dismiss="modal">'.lan('lib.cancel').'</button>'.LF;
    $lReturn.= '      </div>'.LF;
    $lReturn.= '    </div>'.LF;
    $lReturn.= '  </div>'.LF;
    $lReturn.= '</div>'.LF;

    return $lReturn;
  }
}