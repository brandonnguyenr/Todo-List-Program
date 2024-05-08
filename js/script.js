const currentDate = new Date();
const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
document.getElementById('date').textContent = currentDate.toLocaleDateString('en-US', options);

document.getElementById('todo-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const task = document.getElementById('input-box').value;

    const response = await fetch('php/list.php', {
        method: 'POST',
        body: JSON.stringify({ name: task }), 
        headers: {
            'Content-Type': 'application/json'
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    fetchTasks();

    const todoForm = document.getElementById('todo-form');
    todoForm.addEventListener('submit', function(event) {
        event.preventDefault(); 
        location.reload();
    });
});
    
fetch('php/list.php')
    .then(response => response.json())
    .then(data => {
        const todoList = document.getElementById('tasks-container');
        todoList.innerHTML = ''; 
        data.forEach(item => {
            const taskContainer = document.createElement('div'); 
            taskContainer.classList.add('task-container'); 
            const taskItem = document.createElement('span'); 
            taskItem.textContent = item.name; 
            taskContainer.appendChild(taskItem); 
            todoList.appendChild(taskContainer); 
        });
    })
    .catch(error => console.error('Error fetching data:', error));