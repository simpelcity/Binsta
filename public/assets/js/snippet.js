document.addEventListener("DOMContentLoaded", () => {
	const languageSelect = document.getElementById("language-select");

	let languages = [
		{ name: "HTML", value: "html" },
		{ name: "CSS", value: "css" },
		{ name: "JavaScript", value: "js" },
		{ name: "PHP", value: "php" },
		{ name: "Twig", value: "twig " },
	];

	languages.forEach((lang) => {
		languageSelect.innerHTML += `
		<option value="${lang.value}">${lang.name}</option>
		`;
	});
});
