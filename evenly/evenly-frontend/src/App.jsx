import { useEffect, useState } from 'react';

function App() {
  const [tasks, setTasks] = useState([]);

  // useEffect(() => {
  //   fetch('/evenly/backend/tasks.php')
  //     .then(res => res.json())
  //     .then(data => setTasks(data))
  //     .catch(err => console.error(err));
  // }, []);

  return (
    <div>
      <h1>Tasks</h1>
      {/* <ul>
        {tasks.length === 0 
          ? <li>No tasks found</li> 
          : tasks.map(task => <li key={task.id}>{task.name}</li>)
        }
      </ul> */}
    </div>
  );
}

export default App;