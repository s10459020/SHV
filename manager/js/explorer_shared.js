let realPath = '/';
let currentPath = '/';

const api = {
  type: `${config.apiBase}/${config.typeApi}`,
  dirScan: `${config.apiBase}/${config.dirScanApi}`,
  dirCreate: `${config.apiBase}/dir/create_dir.php`,
  dirDelete: `${config.apiBase}/dir/delete_dir.php`,
  dirRename: `${config.apiBase}/dir/rename_dir.php`,
  dirDownload: `${config.apiBase}/dir/download_dir.php`,
  fileScan: `${config.apiBase}/${config.fileScanApi}`,
  fileRead: `${config.apiBase}/${config.fileReadApi}`,
  fileWrite: `${config.apiBase}/file/write_file.php`,
  fileCreate: `${config.apiBase}/file/create_file.php`,
  fileDelete: `${config.apiBase}/file/delete_file.php`,
  fileRename: `${config.apiBase}/file/rename_file.php`,
  fileDownload: `${config.apiBase}/file/download_file.php`,
  fileUploads: `${config.apiBase}/file/upload_files.php`
};

$(document).ready(() => {
  initialDrop();
  currentPath = '/';
  read(currentPath);
});

function normalizePath(path) {
  const tokens = String(path || '/').replace(/\\/g, '/').split('/');
  const out = [];
  for (const t of tokens) {
    if (!t || t === '.') continue;
    if (t === '..') { if (out.length) out.pop(); continue; }
    out.push(t);
  }
  return '/' + out.join('/');
}

function pathBack(path) {
  read(normalizePath(`${path}/..`));
}

async function read(path) {
  currentPath = normalizePath(path);
  realPath = currentPath;
  $('#path').val(currentPath);

  const type = await $.post(api.type, { path: realPath });
  if (type === 'dir') {
    await readDir(realPath);
    $('#listPanel').show();
    $('#viewPanel').hide();
    $('#editPanel').hide();
    return;
  }
  if (type === 'file') {
    readFile(realPath);
    $('#viewPanel').show();
    $('#listPanel').hide();
    $('#editPanel').hide();
  }
}

async function readDir(path) {
  const [dirsRes, filesRes] = await $.when(
    $.post(api.dirScan, { path }),
    $.post(api.fileScan, { path })
  );
  const dirs = JSON.parse(dirsRes[0]);
  const files = JSON.parse(filesRes[0]);
  const dom = $('#drop');
  dom.empty();

  dirs.forEach((d) => dom.append(itemNode(path, d, 'dir')));
  files.forEach((f) => dom.append(itemNode(path, f, 'file')));
}

function itemNode(base, name, type) {
  const li = $('<li></li>');
  const full = normalizePath(`${base}/${name}`);
  const n = $('<span class="name"></span>').text(name).addClass(type);
  n.on('dblclick', () => read(full));
  li.append(n);
  li.append($('<button>改名</button>').on('click', () => rename(full, type)));
  li.append($('<button>刪除</button>').on('click', () => removeItem(full, type)));
  return li;
}

function readFile(path) {
  $('#frame').attr('src', `${api.fileDownload}?path=${encodeURIComponent(path)}`);
}

function edit(path) {
  $.post(api.fileRead, { path }).done((res) => {
    $('#editor').val(res);
    $('#editPanel').show();
    $('#viewPanel').hide();
    $('#listPanel').hide();
  });
}

function save(path) {
  const content = $('#editor').val();
  $.post(api.fileWrite, { path, content }).done(() => read(currentPath));
}

function create(path, type) {
  const u = type === 'dir' ? api.dirCreate : api.fileCreate;
  const payload = type === 'dir' ? { dir: path } : { dir: path };
  $.post(u, payload).done(() => read(currentPath));
}

function rename(path, type) {
  const name = prompt('新名稱');
  if (!name) return;
  const u = type === 'dir' ? api.dirRename : api.fileRename;
  $.post(u, { path, name }).done(() => read(currentPath));
}

function removeItem(path, type) {
  if (!confirm(`確定刪除 ${path} ?`)) return;
  const u = type === 'dir' ? api.dirDelete : api.fileDelete;
  $.post(u, { path }).done(() => read(currentPath));
}

async function download(path) {
  const type = await $.post(api.type, { path });
  const form = $('#downloadForm');
  form.find('input[name=path]').val(path);
  form.attr('action', type === 'dir' ? api.dirDownload : api.fileDownload);
  form.submit();
}

function initialDrop() {
  const drop = $('#drop');
  drop.on('dragover', (e) => e.preventDefault());
  drop.on('drop', async (e) => {
    e.preventDefault();
    const items = e.originalEvent.dataTransfer.items;
    const files = [];
    const dirs = [];
    for (let i = 0; i < items.length; i++) {
      const item = items[i].getAsFile();
      if (!item) continue;
      files.push(item);
      dirs.push(realPath);
    }
    const fd = new FormData();
    files.forEach((f) => fd.append('files[]', f));
    dirs.forEach((d) => fd.append('dirs[]', d));
    await $.ajax({ type: 'POST', url: api.fileUploads, data: fd, processData: false, contentType: false });
    read(currentPath);
  });
}

