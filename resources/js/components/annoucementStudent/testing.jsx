import React, { useState, useEffect} from 'react';
import axios from 'axios';

const TestingComponenet = () => {

    const [data, setData] = useState([]);

    const [value, setValue] = useState(0);

    useEffect(() => {
        const fetchData = async () => {
            try{
                const response = await axios.get('/api/annoucementStudent/testing');
                setData(response.data);
            }catch(error){
                console.error('Error fetching data:', error);
            }
        }

        fetchData();
    }, [value]);

    const countValue = (item) => {
        setValue(value + item);
    }

    return(

        <div>
            <div>
                <h1>Testing Component</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Department</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.map((item, index) => (
                            <tr key={index.id}>
                                <td>{item.id}</td>
                                <td>{item.title}</td>
                                <td>{item.content}</td>
                                <td>{item.start_date}</td>
                                <td>{item.end_date}</td>
                                <td>{item.department}</td>
                                <td>{item.priority}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <div>
                <h1>Testing Component</h1>
                <h2>Value: {value}</h2>
                <input 
                type="number"
                value={value}
                onChange={(e) => setValue(Number(e.target.value))}
                 />
                <button onClick={() => countValue(1)}>Increment</button>
            </div>
        </div>
        
    );
}

export default TestingComponenet;