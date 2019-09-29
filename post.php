<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <meta charset="utf-8">
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <style>
        *{
            color: #2B5DA8;
            font-family: sans-serif;
            font-size: 10pt;
        }
        table{
            border: 5px double #5175FF;
            width: 80%;
        }
        #header{
            background: #C0C3FF;
        }
        th, td{
            border: 2px solid #5175FF;
            text-align: center;
        }
        .time{
            font-weight: bold;
        }
        .messages, .time_area{
            text-align: left;
            float: none;
            margin-left: 5%;
        }
    </style>
</head>
<body>
<?php
    date_default_timezone_set("Europe/Moscow");
    $request_time=date("H:i:s", time());
    $start_time = microtime(true);
    echo "<div class = 'time_area'>Текущее время: <span class = 'time' id='time'></span><br>";
    echo "Время запроса: <span class = 'time'>".$request_time."</span><br>";
    echo "Время вычисления(с): <span class = 'time' id='timedone'></span></div><br>";

    if(session_id()===""){
        session_start();
    }

    $arrayX=array();
    $flagX = false;
    $flagR = true;
    $flagY = true;

    for($i=-3; $i<6; $i++){
        if(isset($_GET['chb'.$i])){
            array_push($arrayX, $_GET['chb'.$i]);
            $flagX = true;
        }
    }

    echo "<div class = 'messages'>";

    if(!$flagX){
        echo "Выберите координату X<br>";
    }
    if(!isset($_GET['Y'])){
        echo "Введите Y<br>";
        $flagY=false;
        $Y=null;
    } else {
        $Y = $_GET['Y'].trim();
        if (!strcmp($Y, "")) {
            echo "Введите Y<br>";
            $flagY = false;
        } else {
            if (!is_numeric(str_replace(',', '.', $_GET['Y']))) {
                echo "Y должен быть числом<br>";
                $flagY = false;
            } else {
                if(substr($_GET['Y'], 0, 1) === '-'&&(float)str_replace(',', '.', $_GET['Y'])==0){
                    $Y=0;
                } else {
                    $Y = (float)str_replace(',', '.', $_GET['Y']);
                    if (($Y <= -5) || ($Y >= 5)) {
                        echo "Y находится вне диапазона<br>";
                        $flagY = false;
                    }
                }
            }
        }
    }

    if(!isset($_GET['R'])){
        echo "Введите R<br>";
        $flagR=false;
        $R=null;
    } else {
        $R = $_GET['R'] . trim();
        if (!strcmp($R, "")) {
            echo "Введите R<br>";
            $flagR = false;
        } else {
            if (!is_numeric(str_replace(',', '.', $_GET['R']))) {
                echo "R должен быть числом<br>";
                $flagR = false;
            } else {
                $R = (float)str_replace(',', '.', $_GET['R']);
                if (($R <= 2) || ($R >= 5)) {
                    echo "R находится вне диапазона<br>";
                    $flagR = false;
                }
            }
        }
    }

    echo "<br></div>";

    if (!isset($_SESSION['points'])) {
         $_SESSION['points'] = array();
    }

    if($flagR&&$flagY&&$flagX){
         foreach ($arrayX as $valueX){
             $point = new Point($valueX, $Y, $R, $request_time);
             array_push($_SESSION['points'], $point);
        }
    }

    echo "<table  align='center'>
    <thead>
    <tr id = 'header'>
    <th><h5>Координата Х</h5></th>
    <th><h5>Координата Y</h5></th>
    <th><h5>Радиус R</h5></th>
    <th><h5>Есть пробитие?</h5></th>
    <th><h5>Время</h5></th>
    </tr>
    </thead>";

foreach (array_reverse($_SESSION['points']) as $point)
{
    echo "<tr>
    <td>$point->x</td>
    <td>$point->y</td>
    <td>$point->r</td>";
    echo $point->check()? "<td>Да</td>" : "<td>Нет</td>";
    echo "<td>$point->time</td>";
    echo "</tr>";
}

    echo "</table>";

$time = (float)round( microtime(true) - $start_time,6);
if ($time==0){
    $time = "Менее 0.000001";
}

class Point{
    public $x;
    public $y;
    public $r;
    public $time;

    function __construct($x,$y,$r, $time)
    {
        $this->x=$x;
        $this->y=$y;
        $this->r=$r;
        $this->time=$time;
    }

    function check(){
        if($this->x==0&&$this->y==0) return true;
        if($this->x<0&&$this->y<0) return false;
        if($this->x>=0&&$this->y>=0) return $this->x<=$this->r&&$this->y<=$this->r;
        if($this->x>=0&&$this->y<=0) {
            return ($this->r*($this->y+$this->r/2)-$this->x*$this->r/2)>=0;
        }
        if($this->x<=0&&$this->y>=0){
           return hypot($this->x,$this->y)<=$this->r/2;
        }
        return false;
    }
}
?>
<script>
    function show()
    {
        $.ajax({
            url: "time.php",
            cache: false,
            success: function(html){
                $("#time").html(html);
            }
        });
    }

    $(document).ready(function(){
        show();
        setInterval('show()',1000);
    });

    document.getElementById('timedone').innerHTML = '<?php echo $time;?>'
</script>
</body>
</html>