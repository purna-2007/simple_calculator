<?php
session_start();

/* Initialize history */
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

/* Delete single history item (POST – safe) */
if (isset($_POST['delete_history'])) {
    $index = $_POST['delete_history'];
    unset($_SESSION['history'][$index]);
    $_SESSION['history'] = array_values($_SESSION['history']);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>PHP Calculator</title>

<style>
body{
    background:#f2f2f2;
    font-family: Arial;
}

.calculator{
    width:300px;
    margin:40px auto;
    background:#fff;
    padding:15px;
    border-radius:15px;
    box-shadow:0 0 15px rgba(0,0,0,0.2);
}

.display{
    width:100%;
    height:55px;
    font-size:26px;
    text-align:right;
    padding:10px;
    border:none;
    background:#eee;
    border-radius:10px;
}

.history{
    margin-top:10px;
    background:#fafafa;
    padding:8px;
    border-radius:8px;
    font-size:14px;
    height:90px;
    overflow-y:auto;
    color:#555;
}

.hist-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:3px;
}

.hist-item button{
    border:none;
    background:none;
    color:red;
    cursor:pointer;
    font-size:14px;
}

.buttons{
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap:10px;
    margin-top:15px;
}

button{
    height:55px;
    font-size:20px;
    border:none;
    border-radius:10px;
    cursor:pointer;
}

.num{ background:#e0e0e0; }
.op{ background:#ff9800; color:white; }
.ac{ background:#f44336; color:white; }
.del{ background:#9e9e9e; color:white; }
.eq{
    background:#ff9800;
    color:white;
    grid-column: span 2;
}
</style>
</head>

<body>

<div class="calculator">
<form method="post">

    <!-- Display -->
    <input class="display" type="text" name="display"
           value="<?php echo $_POST['display'] ?? ''; ?>" readonly>

    <!-- History -->
    <div class="history">
        <?php if (!empty($_SESSION['history'])) { ?>
            <?php foreach ($_SESSION['history'] as $i => $h) { ?>
                <div class="hist-item">
                    <span><?php echo $h; ?></span>
                    <button type="submit" name="delete_history" value="<?php echo $i; ?>">❌</button>
                </div>
            <?php } ?>
        <?php } else { ?>
            <small>No history</small>
        <?php } ?>
    </div>

    <!-- Hidden expression -->
    <input type="hidden" name="exp" id="exp"
           value="<?php echo $_POST['exp'] ?? ''; ?>">

    <!-- Buttons -->
    <div class="buttons">
        <button class="ac" name="clear">AC</button>
        <button class="del" type="button" onclick="backspace()">⌫</button>
        <button class="op" type="button" onclick="add('/')">÷</button>
        <button class="op" type="button" onclick="add('*')">×</button>

        <button class="num" type="button" onclick="add('7')">7</button>
        <button class="num" type="button" onclick="add('8')">8</button>
        <button class="num" type="button" onclick="add('9')">9</button>
        <button class="op" type="button" onclick="add('-')">−</button>

        <button class="num" type="button" onclick="add('4')">4</button>
        <button class="num" type="button" onclick="add('5')">5</button>
        <button class="num" type="button" onclick="add('6')">6</button>
        <button class="op" type="button" onclick="add('+')">+</button>

        <button class="num" type="button" onclick="add('1')">1</button>
        <button class="num" type="button" onclick="add('2')">2</button>
        <button class="num" type="button" onclick="add('3')">3</button>

        <button class="num" type="button" onclick="add('0')">0</button>
        <button class="num" type="button" onclick="add('.')">.</button>
        <button class="eq" name="equal">=</button>
    </div>
</form>
</div>

<script>
let isResultShown = false;

function add(val){
    let exp = document.getElementById("exp");

    if (isResultShown) {
        exp.value = "";
        document.forms[0].display.value = "";
        isResultShown = false;
    }

    // prevent multiple decimals in one number
    if (val === '.' && exp.value.split(/[\+\-\*\/]/).pop().includes('.')) {
        return;
    }

    exp.value += val;
    document.forms[0].display.value = exp.value;
}

function backspace(){
    let exp = document.getElementById("exp");
    exp.value = exp.value.slice(0, -1);
    document.forms[0].display.value = exp.value;
}
</script>

<?php
/* Calculate */
if (isset($_POST['equal'])) {
    $exp = $_POST['exp'];

    if ($exp != "") {
        $result = @eval("return $exp;");
        $_SESSION['history'][] = "$exp = $result";

        echo "<script>
            document.forms[0].display.value = '$result';
            document.getElementById('exp').value = '';
            isResultShown = true;
        </script>";
    }
}

/* Clear all */
if (isset($_POST['clear'])) {
    $_SESSION['history'] = [];
    echo "<script>
        document.forms[0].display.value = '';
        document.getElementById('exp').value = '';
        isResultShown = false;
    </script>";
}
?>

</body>
</html>
