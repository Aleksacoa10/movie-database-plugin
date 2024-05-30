document.addEventListener("DOMContentLoaded", function() {
    let page = 1;
    const loadMoreButton = document.getElementById("load-more-movies");

    loadMoreButton.addEventListener("click", function() {
        page++;
        fetch(`${movieLoadMore.api_url}?page=${page}&per_page=10`)
            .then(response => response.json())
            .then(data => {
                const moviesContainer = document.getElementById("movies-container");
                data.forEach(movie => {
                    const movieElement = document.createElement("div");
                    movieElement.classList.add("movie");
                    movieElement.innerHTML = `
                        <h2><a href="${movie.permalink}">${movie.title}</a></h2>
                        <div>${movie.excerpt}</div>
                        <a href="${movie.permalink}">
                            <img src="${movie.thumbnail}" alt="${movie.title}">
                        </a>
                    `;
                    moviesContainer.appendChild(movieElement);
                });

                // 
                if (data.length < 10) {
                    loadMoreButton.disabled = true;
                    loadMoreButton.textContent = "Load More Movies";
                }
            })
            .catch(error => console.error("Error loading movies:", error));
    });
});
