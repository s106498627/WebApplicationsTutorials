var yourName;   //global variable accessible to all functions

function showAnotherMessage() {
	alert("Hi " + yourName + ".\nThis is an alert message is no longer defined\nin the HTML but in a JavaScript file");

	showCompletionMessage();
}

function init() {
	yourName = prompt('Hi. Enter your name.\nWhen the browser window is first loaded\nthe function containing this prompt window is called.', "Your name");
	rewriteParagraph(yourName);
	
	let clickme = document.getElementById("clickme");
	clickme.onclick = showAnotherMessage;
}

function rewriteParagraph(userName) {
	let msg = document.getElementById("message");
	msg.innerHTML = "<p>HELLO " + userName + ", from JS!</p>";
}

function showCompletionMessage() {
	let msg = document.getElementById("message-completion");
	msg.textContent = "You have now finished task 1!";
}

window.onload = init;