function toggleDeleteMode() {
    let crosses = document.querySelectorAll('.delete-cross');
    crosses.forEach(cross => {
        cross.style.display = (cross.style.display === 'none') ? 'block' : 'none';
    
    });
}