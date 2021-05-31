<?php
$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

if(strpos($url, 'footer') !== false)
{
    header("Location:upload.php");
}
?>

<div class="footer-elements">
    <div class="footer-element" style="text-align: left"> TPI</div>
    <div class="footer-element"> ETML </div>
    <div class="footer-element" style="text-align: right"> CONTACT </div>
    <div class="footer-element secondary-text" style="text-align: left">© Ylli Fazlija</div>
    <div class="footer-element secondary-text" style="width: 33%">Lausanne</div>
    <div class="footer-element secondary-text" style="text-align: right;"><a style="color: darkgray;" href="mailto:ylli.fazlija@eduvaud.ch">ylli.fazlija@eduvaud.ch</a></div>
</div>
<div class="light-bar">
    <div style="height: 18px">
    </div>
</div>