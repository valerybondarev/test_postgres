<script src="js/jquery-3.3.1.js"></script>

<label for="input_feed">Ссылка:</label>
<input type="text" id="input_feed">
<button type="button" onClick="setFeed()">Надо нажать!</button>

<script>
    function setFeed(){
        $.ajax({
            'url':'parse_xml.php',
            'type': 'post',
            'data': {feed: $('#input_feed').val()},
            success: function () {
                alert(1);
                document.location.href = 'feed.php'
            }
        });
    }
</script>