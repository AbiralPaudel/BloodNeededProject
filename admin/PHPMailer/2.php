<input type="text" id="result" readonly>
<button onclick="calc('1')">1</button>
<button onclick="calc('+')">+</button>
<button onclick="calc('2')">2</button>
<button onclick="calculate()">=</button>
<script>
function calc(val) { document.getElementById("result").value += val; }
function calculate() { document.getElementById("result").value = eval(document.getElementById("result").value); }
</script>
