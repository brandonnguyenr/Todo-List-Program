const currentDate = new Date();
const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
document.getElementById('date').textContent = currentDate.toLocaleDateString('en-US', options);

document.getElementById('todo-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const task = document.getElementById('input-box').value;

    try {
        const response = await fetch('php/list.php', {
            method: 'POST',
            body: JSON.stringify({ name: task }), 
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to add task');
        }

        fetchTasks();
    } catch (error) {
        console.error('Error adding task:', error);
    }

    location.reload(); 
});

document.addEventListener('DOMContentLoaded', function() {
    fetchTasks();
});

function fetchTasks() {
    fetch('php/list.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch tasks');
            }
            return response.json();
        })
        .then(data => {
            const todoList = document.getElementById('tasks-container');
            todoList.innerHTML = ''; 
            data.forEach(item => {
                const taskContainer = document.createElement('div'); 
                taskContainer.classList.add('task-container'); 
                taskContainer.dataset.taskId = item.id; 
                const taskItem = document.createElement('span'); 
                taskItem.textContent = item.name; 
                taskContainer.appendChild(taskItem); 

                // Create the pencil icon button
                const addButton = document.createElement('button');
                addButton.classList.add('add-text-button'); // Add a class for styling
                addButton.type = 'button'; 
                const pencilIcon = document.createElement('img');
                pencilIcon.src = 'images/pencil-icon-vector-21894351.jpg'; // Provide the path to your pencil icon image
                pencilIcon.alt = 'Add Text';
                addButton.appendChild(pencilIcon);
                addButton.addEventListener('click', function(event) {
                    if (event.target === pencilIcon) {
                        const taskId = taskContainer.dataset.taskId;
                        const newText = prompt('Enter your text:');
            
                        if (!newText) {
                            return;
                        }
                        saveText(taskId, newText);
                    }
                });
                taskContainer.appendChild(addButton);

                // Create the delete button
                const deleteButton = document.createElement('button');
                deleteButton.classList.add('delete-button');
                deleteButton.type = 'button'; 
                const trashCanIcon = document.createElement('img');
                trashCanIcon.src = 'images/trash-can-outline-icon-bin-vector-43707178.jpg'; 
                trashCanIcon.alt = 'Delete';
                deleteButton.appendChild(trashCanIcon);
                deleteButton.addEventListener('click', function() {
                    const taskId = taskContainer.dataset.taskId;
                    deleteTask(taskId);
                });
                taskContainer.appendChild(deleteButton);

                todoList.appendChild(taskContainer); 
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}


document.getElementById('tasks-container').addEventListener('click', function(event) {
    if (event.target.classList.contains('delete-button')) {
        const taskContainer = event.target.closest('.task-container');
        const taskId = taskContainer.dataset.taskId;
        deleteTask(taskId);
    }
});

function deleteTask(taskId) {
    const confirmDelete = confirm("Are you sure you want to delete this task?");

    if (!confirmDelete) {
        return;
    }

    fetch('php/list.php?list_id=' + taskId, {
        method: 'DELETE'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete task');
        }
        return response.json();
    })
    .then(data => {
        console.log(data); 
        const taskContainer = document.querySelector(`.task-container[data-task-id="${taskId}"]`);
        taskContainer.remove();
    })
    .catch(error => console.error('Error deleting task:', error));
} 

async function saveText(listId, newText) {
    try {
        const response = await fetch('php/list.php', {
            method: 'PUT',
            body: JSON.stringify({
                list_id: listId,
                item: {
                    text: newText
                }
            }), 
            headers: {
                'Content-Type': 'application/json'
            }
        });

        console.log('Response received:', response);

        if (!response.ok) {
            throw new Error('Failed to save text');
        }

        fetchTasks();
    } catch (error) {
        console.error('Error saving text:', error);
    }
}
