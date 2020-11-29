<?php
date_default_timezone_set('Asia/Tokyo');

// 年月日を取得
$year = date('Y');
$month = date('n');
$day = date('d');
//曜日
$weekday = array( "日", "月", "火", "水", "木", "金", "土" );
// 曜日を数字で
$week = date('w', mktime(0, 0, 0, $month, $i, $year));
// 月末日を取得
$last_day = date( 't' , strtotime($year. $month."01"));
//1日の曜日を取得
$first_week = date('w', strtotime($year.$month.'01'));
//月末日の曜日を取得
$last_week = date('w', strtotime($year.$month.$last_day));

$aryCalendar = [];
$j = 0;

$calendar = array();
$j = 0;

//1日開始曜日までの穴埋め
for($i = 0; $i < $first_week; $i++){
    $aryCalendar[$j][] = '';
}

//1日から月末日までループ
for ($i = 1; $i <= $last_day; $i++){
    //日曜日まで進んだら改行
    if(isset($aryCalendar[$j]) && count($aryCalendar[$j]) === 7){
        $j++;
    }
    $aryCalendar[$j][] = $i; 
}
//月末曜日の穴埋め
for($i = count($aryCalendar[$j]); $i < 7; $i++){
    $aryCalendar[$j][] = '';
}

$dataFile = 'a.dat';

function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}


//入出力

if (isset($_POST['toukou'])){

    $lines = file('a.dat');
    $cnt = count($lines);
    $cnt += 1;

    $task = $_POST['task'];
    $time = $_POST['time'];

    $newData = "{番号}" . "<" . $cnt . ">" . "\t".  $time . "\t" .$task. "\n";

     write($dataFile, $newData, "a");
}

$posts = file($dataFile, FILE_IGNORE_NEW_LINES);
$posts = array_reverse($posts);

if (isset($_POST['delete']))
{
    for ($i = 0; $i < count($posts); $i++)
    {
        $items = explode("\t", $posts[$i]);
        if($items[0] == "{番号}<{$_POST['delno']}>")
        {
            $posts[$i] = "";
        }
    }
    
    $newData = array_reverse($posts);
    $newData = implode("\n", $newData) . "\n";

    write($dataFile, $newData, "w");
}

$all_posts = count($posts);
foreach($posts as $post)
{
    if(empty($post)){ $all_posts--; }
}

function write($dataFile, $newData, $mode)
{
    $fp = fopen($dataFile, $mode);
    fwrite($fp, $newData);
    fclose($fp);
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="style.css">
 <meta charaset="UTF-8">
 <title>カレンダー</title>
</head>
<body>
<h1><?php echo $month; ?>月のカレンダー</h1>
<p>本日は<?php echo $year; ?>年<?php echo date('n'); ?>月<?php echo $day; ?>日です</p>

<div class="main">
<div class="form">
<table class="calendar">
    <!-- 曜日の表示 -->
    <tr>
    <?php foreach($weekday as $week){ ?>
        <th><?php echo $week ?></th>
    <?php } ?>
    </tr>

    <!-- 日数の表示 -->
    <?php foreach($aryCalendar as $tr){ ?>
    <tr>
        <?php foreach($tr as $td){ ?>
            <?php if($td != date('j')){ ?>
                <td><?php echo $td ?></td>
            <?php }else{ ?>
                <!-- 今日の日付 -->
                <td class="today"><?php echo $td ?></td>
            <?php } ?>
        <?php } ?>
    </tr>
    <?php } ?>
 </table>
 <br>
   
    <!-- タイピングせずにタスクの入力、削除したい -->
 <form  action="" method="POST">
    <div>
         <label for="time" name="name">時間</label>
         <label for="task" task="task">タスク</label>
         <br>
  <input type="time" id="time" name="time">
    <select  name="task" id="task">
     <option value="起床">起床</option>
     <option value="風呂">風呂</option>
     <option value="読書">読書</option>
     <option value="勉強">勉強</option>
     <option value="食事">食事</option>
    </select>
   </div>
  <br>
   <input type="submit" name="toukou" value="送信"><input type="reset" value="リセット">
 </form>
 <form method="post" action="">
            削除指定番号：<input type="text" name="delno"> <input type="submit" name="delete" value="削除">
 </form>           
 </div>

<!-- 出力 -->
<div>
<h2>タスク一覧 (<?php echo $all_posts; ?>個)</h2>
        <ul>
<?php if (count($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <?php if (empty($post)) continue; ?>
        <?php list($cnt, $task, $time) = explode("\t", $post); ?>
                    <li><?php echo h($cnt); ?> <?php echo h($task); ?> <?php echo h($time); ?></li>
                <?php endforeach ?>
            <?php else: ?>
                <li>まだタスクはありません。</li>
            <?php endif; ?>
</div>
</div>
</body>
</html>
