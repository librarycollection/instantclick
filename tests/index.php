<?php
$pages = array(
  'Index page' => '',
  'First test page' => '1',
  'Second test page' => '2',
  'Page without title' => 'no-title',
  'Minimal markup' => 'minimal',
  'NProgress' => 'nprogress',
);

$page = 'welcome';
if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
  $page = $_GET['page'];
}

$preload_on = 'hover';
if (isset($_GET['on'])) {
  if ((int)$_GET['on'] != 0) {
    $preload_on = (int)$_GET['on'];
  }
  elseif ($_GET['on'] == 'mousedown') {
    $preload_on = 'mousedown';
  }
}

$nocache = '&amp;nocache=' . microtime(true) * 10000;
if ($preload_on == 'hover') {
  $append = $nocache;
}
else {
  $append = '&amp;on=' . $preload_on . $nocache;
}

if (isset($_GET['wait'])) {
   usleep((int)$_GET['wait'] * 1000);
}
?>
<!doctype html>
<meta charset="utf-8">
<?php if ($page != 'no-title'): ?>
<title><?php echo date('H : i : s') ?> . <?php printf("%03d", microtime() * 1000) ?></title>
<?php endif ?>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=600">

<?php if ($page == 'nprogress'): ?>
<link rel="stylesheet" href="vendors/nprogress/nprogress-0.1.2.css">
<script src="vendors/jquery/jquery-2.1.0.js"></script>
<script src="vendors/nprogress/nprogress-0.1.2.js"></script>
<?php endif ?>

<?php if ($page == 'minimal'): ?><body>Hiya.<?php exit; endif ?>

<body>

<div id="preloading-level">
  <a data-no-instant href="?<?php echo $nocache ?>" class="<?php if ($preload_on == 'hover') echo 'selected' ?>">↻ On hover</a>
  <a data-no-instant href="?on=100<?php echo $nocache ?>" class="<?php if ($preload_on === (int)$preload_on) echo 'selected' ?>">↻ On hover + 100 ms delay</a>
  <a data-no-instant href="?on=mousedown<?php echo $nocache ?>" class="<?php if ($preload_on == 'mousedown') echo 'selected' ?>">↻ On mousedown</a>
</div>

<hr>

<?php $cols = array(100, 200, 300, 400, 500, 1000, 1500, 2000, 10000) ?>
<table>
  <tr>
    <th>Page</th>
    <th colspan="<?php echo count($cols) ?>">Delays (in milliseconds)</th>

<?php foreach ($pages as $name => $row): ?>
  <tr>
    <td><a href="?<?php echo ($row != '' ? ('page=' . $row) : '') . $append ?>"><?php echo $name ?></a>
<?php if ($row == 'nprogress'): ?>
        <a data-no-instant href="?page=nprogress<?php echo $append ?>">↻</a>
<?php endif ?>
<?php foreach ($cols as $col): ?>
    <td><a href="?<?php echo ($row != '' ? ('page=' . $row) : '') . '&amp;wait=' . $col . $append ?>"><small><?php echo $col ?></small></a>
<?php endforeach;
endforeach ?>
</table>

<hr>

<?php include $page . '.html' ?>

<div id="divDebug"></div>



<script src="<?php if (!file_exists('instantclick.js')) echo '../' ?>instantclick.js?<?php echo $nocache ?>" data-no-instant></script>


<?php if ($page == 'nprogress'): ?>
<script data-no-instant>
InstantClick.on('click', function() {
  NProgress.start()
})

InstantClick.on('change', function(isInitialLoad) {
  if (isInitialLoad) {
    addDebugMessage('NProgress on')
  }
  NProgress.done()
})
</script>
<?php endif ?>

<script data-no-instant>
var $debugMessages = ''

function addDebugMessage(message) {
  var divDebug = document.getElementById('divDebug')
  if (!divDebug) {
    return
  }
  $debugMessages = message + '<br>' + (!divDebug.innerHTML && $debugMessages ? '<hr>' : '') + $debugMessages
  divDebug.innerHTML = $debugMessages
}

InstantClick.on('change', function(isInitialLoad) {
  addDebugMessage('Event: change' + (isInitialLoad ? ' (initial load)' : ''))
})

InstantClick.init(<?php
if ($preload_on == 'mousedown') echo "'mousedown'";
elseif ((int)$preload_on != 0) echo $preload_on;
?>);
</script>