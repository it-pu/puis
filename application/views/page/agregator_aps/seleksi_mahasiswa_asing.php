<h3 align="center">Mahasiswa Asing</h3><br/>Prodi : <span id="viewProdiID"></span> | <span id="viewProdiName"></span>

<script>
function loadPage(){var e=$("#filterProdi").val();""!=e&&null!=e&&($("#viewProdiID").html(e),$("#viewProdiName").html($("#filterProdi option:selected").text()))}$(document).ready(function(){var e=setInterval(function(){var l=$("#filterProdi").val();""!=l&&null!=l&&(loadPage(),clearInterval(e))},1e3);setTimeout(function(){clearInterval(e)},5e3)}),$("#filterProdi").change(function(){var e=$("#filterProdi").val();""!=e&&null!=e&&loadPage()});</script>