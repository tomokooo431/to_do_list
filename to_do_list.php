<?php 
    $todoList = array();
    session_start();
    $i = 0;

    if(isset($_SESSION["todoList"])){
        $todoList = $_SESSION["todoList"];
        if(count($_SESSION["todoList"]) > 0){
            $i = end($todoList)["id"] + 1;
        }
    }

    /** 替換要改變的值進行更新 */
    function updateToDoList ($id, $newItem, $todoList) {
            // 移除
            $todoList = array_filter($todoList, function($n) use ($id) { 
                return $n['id'] !== $id; 
            });

            // 新增
            if(!is_null($newItem)){
                array_push($todoList, $newItem);
            };

            // 排序
            $arr = array_column($todoList, 'id');
            array_multisort($arr, SORT_ASC, $todoList);

            $_SESSION["todoList"] = $todoList;
    }

    /** 取得該id所屬的array內容 */
    function getEditArr ($todoList, $id) {
        $keyFound = null;
        foreach ($todoList as $key => $item) {
            if (isset($item['id']) && $item['id'] === $id) {
                $keyFound = $key;
                break;
            }
        }
        return $todoList[$keyFound];
    }

    if(isset($_POST["act"])){
        if($_POST["act"] === 'clear'){
            $i = 0;
            unset($_SESSION["todoList"]);
            $todoList = [];
        }else if($_POST["act"] === 'add'){
            if($_POST["content"]){
                array_push($todoList, $_POST);
            }
            $i++;
            $_SESSION["todoList"] = $todoList;
        }else if(explode("-" ,$_POST["act"])[0] === 'edit'){
            $id = explode("-" ,$_POST["act"])[1];
            $newItem = getEditArr($todoList, $id);
            $newItem["isEdit"] = true;
            updateToDoList($id, $newItem, $todoList);
        }else if(explode("-" ,$_POST["act"])[0] === 'check'){
            $id = explode("-" ,$_POST["act"])[1];
            $newItem = getEditArr($todoList, $id);
            $newItem["isEdit"] = false;
            $newItem["content"] = $_POST["newContent-$id"];
            updateToDoList($id, $newItem, $todoList);
        }else if(explode("-" ,$_POST["act"])[0] === 'delete'){
            $id = explode("-" ,$_POST["act"])[1];
            updateToDoList($id, null, $todoList);
        }else if(explode("-", $_POST["act"])[0] === 'isChecked'){
            $id = explode("-", $_POST["act"])[1];
            $isChecked = explode("-", $_POST["act"])[2];
            $newItem = getEditArr($todoList, $id);
            $newItem["isChecked"] = $isChecked;
            updateToDoList($id, $newItem, $todoList);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-do List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">
    <style>
        *{
            margin: 0;
            padding: 0;
            font-family: "Noto Sans TC", sans-serif;
        }

        .hidden{
            display: none;
        }

        button, ul, li{
            border: none;
            box-shadow: none;
            background: none;
            text-decoration: none;
        }

        .action-button{
            cursor: pointer;
        }

        body {
            background-color: #D8DDEF;
        }

        body .main-container{
            padding: 0 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            gap: 30px;
        }

        .title-container,
        .to-do-container{
            width: 100%;
            max-width: 680px;
            padding: 15px 20px;
            border-radius: 10px;
        }

        .title-container{
            background-color: #665687;
        }

        .title-container h1{
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            color: white;
        }

        .title-container p{
            color: #D8DDEF;
            text-align: center;
        }

        .to-do-container{
            background-color: #FFF;
            height: 540px;
        }

        .to-do-container p:nth-child(1) {
            font-size: 20px;
            font-weight: 600;
        }

        .to-do-container ul {
            height: calc(540px - 140px);
            overflow: auto;
        }


        .to-do-container p:nth-child(2) {
            font-size: 16px;
            font-weight: 600;
            color: #665687;
        }

        .to-do-container .form-header{
            width: 100%;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 25px;
        }

        .to-do-header {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .label-button{
            color: #665687;
            cursor: pointer;
        }

        .to-do-container .to-do-header .label-button {
            font-size: 16px;
            background: none;
            color: #665687;
            padding: 10px 18px;
        }

        .to-do-container .to-do-header .label-button:active{
            font-size: 16px;
            background: none;
            color: #554870;
        }

        .to-do-container .form-header input,
        .to-do-container .to-do-list .to-do-item input{
            outline: none;
            width: 100%;
            border: none;
            background-color: #D8DDEF;
            padding: 12px 25px;
            border-radius: 10px;
            border: #D8DDEF 1.5px solid;
            transition: border 0.3s;
            font-size: 16px;
            color: #554870;
        }

        .to-do-container .form-header input:focus,
        .to-do-container .to-do-list .to-do-item input:focus{
            border-color: #665687;
        }

        .to-do-container .form-header input::placeholder{
            color: white;
        }

        .to-do-container #to_do_form .label-button{
            border: none;
            box-shadow: none;
            padding: 10px 18px;
            border-radius: 10px;
            background-color: #665687;
            white-space: nowrap;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }

        .to-do-container #to_do_form .label-button:active{
            background-color: #554870;
        }

        .to-do-container .to-do-list{
            margin-top: 12px;
        }

        .to-do-container .to-do-list .to-do-item{
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            color: black;
            padding: 12px 0;
        }

        .to-do-container .to-do-list .to-do-item p,
        .to-do-container .to-do-list .to-do-item input{
            width: 100%;
            color: black;
            font-weight: 400;
            font-size: 16px;
        }

        .to-do-container .to-do-list .to-do-item input{
            border: 1.5px solid #D8DDEF;
            background: #D8DDEF;
            padding: 8px 16px;
            border-radius: 10px;
            color: #554870;
        }

        .checkbox-container{
            width: 100%;
            color: #554870;
            position: relative;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            user-select: none;
            margin-right: 30px;
            gap: 10px;
        }

        .checkbox-inner{
            flex-shrink: 0;
            width: 100%;
            display: inline-block;
            position: relative;
            border: #665687 1px solid;
            border-radius: 3px;
            box-sizing: border-box;
            width: 14px;
            height: 14px;
            background-color: #fff;
            z-index: 1;
            transition: border-color .25s cubic-bezier(.71,-.46,.29,1.46), background-color .25s cubic-bezier(.71,-.46,.29,1.46), outline .25s cubic-bezier(.71,-.46,.29,1.46);
        }

        .checkbox-container input.checked ~ .checkbox-inner{
            background-color: #665687;
            border-color:#665687;
        }

        .checkbox-container input.checked ~ .checkbox-inner:after {
            transform: rotate(45deg) scaleY(1);
            border-color: white;
        }

        .checkbox-inner:after{
            box-sizing: content-box;
            content: "";
            border: 1.5px solid transparent;
            border-left: 0;
            border-top: 0;
            height: 7px;
            left: 4px;
            position: absolute;
            top: 1px;
            transform: rotate(45deg) scaleY(0);
            width: 4px;
            transition: transform .15s ease-in .05s;
            transform-origin: center;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="title-container">
            <h1>TO - DO LIST</h1>
            <p>Keep track of your daily to-do list.</p>
        </div>

        <form class="to-do-container" action="to_do_list.php" method="post">
            <div class="to-do-header">
                <div>
                    <p><?php echo date_format(date_create("now", timezone_open("Asia/Taipei")),"Y - m - d"); ?></p>
                    <p><?php echo isset($_SESSION["todoList"]) ? count($_SESSION["todoList"]) : 0 ?> tasks</p>
                </div>
                <label for="clear" class="label-button">
                    <span class="action-button">Clear All</span>
                    <input class="hidden" id="clear" type="submit" name="act" value="clear"></input>
                </label>
            </div>
            <!-- 新增 -->
            <div class="form-header" action="to_do_list.php" method="post" id="to_do_form">
                <input type="hidden" name="id" value="<?php echo $i; ?>" />
                <input type="hidden" name="isEdit" value="<?php echo false; ?>" />
                <input type="hidden" name="isChecked" value="<?php echo false; ?>" />
                <input type="hidden" name="created_at" value="<?php echo date_format(date_create("now", timezone_open("Asia/Taipei")),"Y/m/d H:i:s"); ?>" />
                <input type="text" placeholder="Please enter the content"  name="content">
                <label class="label-button" for="form-add">
                    <span class="action-button">ADD IT</span>
                    <input class="hidden" id="form-add" type="submit" name="act" value="add"></input>
                </label>
            </div>
            <!-- 顯示列表 -->
            <div class="to-do-list">
                <ul>
                    <?php if(isset($_SESSION["todoList"])) { ?>
                        <?php foreach ($_SESSION["todoList"] as $listItem) { ?>
                            <li class="to-do-item">
                                <?php if ($listItem["isEdit"]) { ?>
                                    <input type="text" name="newContent-<?php echo $listItem["id"]; ?>" value="<?php echo $listItem["content"]; ?>" />
                                    <label class="label-button" for="form-check-<?php echo $listItem["id"]; ?>">
                                        <span>CHECK</span>
                                        <input class="hidden" id="form-check-<?php echo $listItem["id"]; ?>" type="submit" name="act" value="check-<?php echo $listItem["id"]; ?>" />
                                    </label>
                                <?php } else { ?>
                                    <label for="form-isChecked-<?php echo $listItem["id"]; ?>" class='checkbox-container'>
                                        <input class="hidden <?php echo $listItem['isChecked'] ? 'checked' : '' ?>" id="form-isChecked-<?php echo $listItem["id"]; ?>" type="submit" name="act" value="isChecked-<?php echo $listItem["id"]; ?>-<?php echo !$listItem['isChecked'] ?>" />
                                        <span class='checkbox-inner'></span>
                                        <p><?php echo $listItem["content"]; ?></p>
                                    </label>
                                    <label class="label-button" for="form-edit-<?php echo $listItem["id"]; ?>">
                                        <span>EDIT</span>
                                        <input class="hidden" id="form-edit-<?php echo $listItem["id"]; ?>" type="submit" name="act" value="edit-<?php echo $listItem["id"]; ?>" />
                                    </label>
                                    <label class="label-button" for="form-delete-<?php echo $listItem["id"]; ?>">
                                        <span>DELETE</span>
                                        <input class="hidden" id="form-delete-<?php echo $listItem["id"]; ?>" type="submit" name="act" value="delete-<?php echo $listItem["id"]; ?>" />
                                    </label>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
    </form>
    </div>
</body>
</html>