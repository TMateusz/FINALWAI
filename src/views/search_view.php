<?php $title = 'Wyszukiwanie'; ?>

<h2>Wyszukiwanie zdjęć</h2>

<script>
function showResult(str) {
    if (str.length==0) {
        document.getElementById("livesearch").innerHTML="";
        return;
    }
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById("livesearch").innerHTML=this.responseText;
        }
    }
    xmlhttp.open("GET", "/search?q=" + encodeURIComponent(str) + "&ajax=1", true);
    xmlhttp.send();
}
</script>

<label>Wyszukaj: <input type="text" onkeyup="showResult(this.value)" style="padding: 5px; width: 300px;"></label>

<div id="livesearch" style="margin-top: 20px;"></div>
