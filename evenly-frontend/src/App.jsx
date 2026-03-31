import { useEffect, useState } from 'react';
import axios from 'axios';

function App() {
  const [tasks, setTasks] = useState([]);

  useEffect(() => {
    axios.get('http://127.0.0.1:8000/api/tasks')
      .then(res => setTasks(res.data))
      .catch(err => console.error(err));
  }, []);

  return (
    <div>
      <h1>Tasks</h1>
      <ul>
        {tasks.map(task => <li key={task.id}>{task.name} - {task.points} pts</li>)}
      </ul>
    </div>
  );
}

export default App
