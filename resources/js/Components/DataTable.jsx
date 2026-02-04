// resources/js/components/DataTable.jsx

import React, { useState, useEffect } from 'react';
import axios from 'axios';

const DataTable = () => {
    const [data, setData] = useState([]);
    const [filters, setFilters] = useState({
        flatness_status: '',
        group1_1: '',
        // Add other filters as needed
    });

    useEffect(() => {
        fetchData();
    }, [filters]);

    const fetchData = async () => {
        const response = await axios.get('/data-records', { params: filters }); // Use the correct endpoint
        setData(response.data);
    };

    const handleFilterChange = (e) => {
        const { name, value } = e.target;
        setFilters(prevFilters => ({
            ...prevFilters,
            [name]: value,
        }));
    };

    return (
        <div>
            <h2>Data Records</h2>
            <div>
                <label>
                    Flatness Status:
                    <input type="text" name="flatness_status" value={filters.flatness_status} onChange={handleFilterChange} />
                </label>
                {/* Add more filter inputs as needed */}
                <button onClick={fetchData}>Filter</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barcodes 1</th>
                        <th>Resistance 1</th>
                        <th>Voltage 1</th>
                        <th>Flatness Status</th>
                        {/* Add more columns as needed */}
                    </tr>
                </thead>
                <tbody>
                    {data.map(record => (
                        <tr key={record.id}>
                            <td>{record.id}</td>
                            <td>{record.barcodes_1}</td>
                            <td>{record.resistance_1}</td>
                            <td>{record.voltage_1}</td>
                            <td>{record.flatness_status}</td>
                            {/* Add more cells as needed */}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default DataTable;
