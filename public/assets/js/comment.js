document.addEventListener("DOMContentLoaded", () => {
	const commentsState = {};

	const commentForms = document.querySelectorAll(".post-comment");

	commentForms.forEach((form) => {
		const input = form.querySelector(".comment-input");
		const button = form.querySelector(".post-comment-btn");

		input.addEventListener("input", () => {
			if (input.value.trim() === "") {
				button.disabled = true;
			} else {
				button.disabled = false;
			}
		});
	});

	async function showMoreComments(snippetId) {
		const commentsDiv = document.getElementById(`comments-${snippetId}`);
		const showMoreButton = document.querySelector(`.show-more-comments[data-snippet-id="${snippetId}"]`);
		const hideCommentsButton = document.querySelector(`.hide-comments[data-snippet-id="${snippetId}"]`);

		if (!commentsState[snippetId]) {
			commentsState[snippetId] = 0;
		}

		try {
			const limit = 2;
			const response = await fetch(
				`/comment?snippet_id=${snippetId}&offset=${commentsState[snippetId]}&limit=${limit}`
			);

			if (!response.ok) {
				throw new Error("Failed to fetch comments");
			}

			const data = await response.json();
			const comments = data.comments || [];

			comments.forEach((comment) => {
				const hasPfp = comment.pfp && comment.pfp.trim() !== "";
				const theme = localStorage.getItem("theme") || "light";
				const defaultPfp = `/assets/icons/${theme}/profile-user.png`;
				const pfpSrc = hasPfp ? `/assets/uploads/${comment.pfp}` : defaultPfp;
				commentsDiv.innerHTML += `
				<div class="comment mb-2">
					<div class="d-flex">
						<a href="/user/profile/${comment.user_id}" class="d-flex align-items-center text-decoration-none text-black">
							<img src="${pfpSrc}" alt="User" class="icon theme-icon me-2 rounded-circle" />
							<b class="me-2 mb-0">${comment.username}</b>
						</a>
						<p class="m-0">${comment.comment}</p>
					</div>
					<small class="text-info">${comment.created_at.toUpperCase()}</small>
				</div>
				`;
			});

			commentsState[snippetId] += comments.length;

			if (commentsState[snippetId] >= data.total) {
				showMoreButton.classList.add("d-none");
			}

			hideCommentsButton.classList.remove("d-none");
		} catch (error) {
			console.error("Error fetching comments:", error);
		}
	}

	function hideComments(snippetId) {
		const commentsDiv = document.getElementById(`comments-${snippetId}`);
		const showMoreButton = document.querySelector(`.show-more-comments[data-snippet-id="${snippetId}"]`);
		const hideCommentsButton = document.querySelector(`.hide-comments[data-snippet-id="${snippetId}"]`);

		commentsDiv.innerHTML = "";
		commentsState[snippetId] = 0;

		showMoreButton.classList.remove("d-none");
		hideCommentsButton.classList.add("d-none");
	}

	document.querySelectorAll(".show-more-comments").forEach((button) => {
		button.addEventListener("click", () => {
			const snippetId = button.dataset.snippetId;
			showMoreComments(snippetId);
		});
	});

	document.querySelectorAll(".hide-comments").forEach((button) => {
		button.addEventListener("click", () => {
			const snippetId = button.dataset.snippetId;
			hideComments(snippetId);
		});
	});
});
