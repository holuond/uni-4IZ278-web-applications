<?php
    function redirectToPageWithPost($pageRelPath, $balloonMessage, $balloonType)
    {
        ?>
        <form name='del' action='<?php echo($pageRelPath) ?>' method='POST'>
            <input type='hidden' name='balloonMessage' value='<?php echo($balloonMessage) ?>'>
            <input type='hidden' name='balloonType' value='<?php echo($balloonType) ?>'>
        </form>
        <script type='text/javascript'>
            document.del.submit();
        </script>
        <?php
    }

?>

