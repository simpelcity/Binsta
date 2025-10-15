document.addEventListener("DOMContentLoaded", () => {
	const languageSelect = document.getElementById("language-select");

	let languages = [
		{ name: "HTML", value: "html" },
		{ name: "CSS", value: "css" },
		{ name: "C-like", value: "clike" },
		{ name: "JavaScript", value: "javascript" },
		{ name: "C", value: "c" },
		{ name: "C#", value: "csharp" },
		{ name: "C++", value: "cpp" },
		{ name: "JSON", value: "json" },
		{ name: "Markdown", value: "markdown" },
		{ name: "PHP", value: "php" },
		{ name: "Python", value: "python" },
		{ name: "React JSX", value: "jsx" },
		{ name: "React TSX", value: "tsx" },
		{ name: "Sass", value: "scss" },
		{ name: "SQL", value: "sql" },
		{ name: "Twig", value: "twig " },
		{ name: "TypeScript", value: "typescript" },
	];

	languages.forEach((lang) => {
		languageSelect.innerHTML += `
		<option value="${lang.value}">${lang.name}</option>
		`;
	});
});
