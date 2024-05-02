<script>
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.body.innerHTML = xhr.responseText;
    }
  };
  xhr.open("GET", "resources/views/", true);
  xhr.send();
</script>
