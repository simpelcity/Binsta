document.addEventListener("DOMContentLoaded", () => {
	const languageSelect = document.getElementById("language-select");

	let languages = [
		{ name: "HTML", value: "html" },
		{ name: "CSS", value: "css" },
		{ name: "JavaScript", value: "js" },
		{ name: "PHP", value: "js" },
	];

	languages.forEach((lang) => {
		languageSelect.innerHTML += `
		<option value="${lang.value}">${lang.name}</option>
		`;
	});
});
