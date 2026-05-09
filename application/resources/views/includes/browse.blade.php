<?php
function is_in_iframe() {
    return (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '' && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false);
}

if(is_in_iframe()) {
?>
<div style="height: 100vh; width: 100%; background: rgb(255 255 255 / 96%); z-index: 100; position: fixed; display: flex; justify-content: center; align-items: center">
    <div style="text-align: center;
        border: 1px solid #d1d1d1;
        z-index: 100;
        padding: 10px 34px;
        border-radius: 5px;
        background: #ffec5e; opacity: 1">
        <a target="_blank" style="color: black; text-decoration: underline; font-weight: bold; font-size: 18px" href="{{route('home')}}">Browse Directly for Better Experience</a>
    </div>
</div>

<?php
} ?>
