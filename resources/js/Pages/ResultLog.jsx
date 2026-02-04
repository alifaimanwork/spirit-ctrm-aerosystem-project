import { useState, useEffect } from 'react';
import { Head } from "@inertiajs/react";
import Layout from "@/Layouts/Layout";
import axios from 'axios';
import DatePicker from 'react-datepicker';
import "react-datepicker/dist/react-datepicker.css";
import Report from '@/Components/Report';

export default function ResultLog() {
    const [searchQuery, setSearchQuery] = useState('');
    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [status, setStatus] = useState('ALL');
    const [results, setResults] = useState([]);
    const [filteredResults, setFilteredResults] = useState([]);

    // Fetch initial data
    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get(route('comparison.results'));
                setResults(response.data);
                setFilteredResults(response.data);
            } catch (error) {
                console.error('Error fetching results:', error);
            }
        };
        fetchData();
    }, []);

    // Apply filters when search, dates, or status changes
    useEffect(() => {
        let filtered = [...results];

        // Search filter
        if (searchQuery) {
            filtered = filtered.filter(item => 
                item.joborder.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.partno.toLowerCase().includes(searchQuery.toLowerCase())
            );
        }

        // Date filter
        if (startDate && endDate) {
            filtered = filtered.filter(item => {
                const itemDate = new Date(item.created_at.replace(' ', 'T')); // Convert MySQL datetime to JS Date
                // Set time to start of day for startDate and end of day for endDate
                const start = new Date(startDate.setHours(0, 0, 0, 0));
                const end = new Date(endDate.setHours(23, 59, 59, 999));
                return itemDate >= start && itemDate <= end;
            });
        }

        // Status filter
        if (status !== 'ALL') {
            filtered = filtered.filter(item => 
                status === 'GOOD' ? item.status === 'match' : item.status === 'mismatch'
            );
        }

        setFilteredResults(filtered);
    }, [searchQuery, startDate, endDate, status, results]);

    const clearSearch = () => {
        setSearchQuery('');
    };

    return (
        <Layout>
            <Head title="Result Log" />
            
            <div className="min-h-screen bg-[#1E1E1E] p-6">
                <div className="bg-[#0A0A29] rounded-lg p-6">
                    {/* Filters Section */}
                    <div className="flex justify-between items-center mb-6">
                        {/* Search Bar */}
                        <div className="relative w-96">
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Search job order or part no..."
                                className="w-full p-2 pr-10 rounded bg-white text-black"
                            />
                            {searchQuery && (
                                <button
                                    onClick={clearSearch}
                                    className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                >
                                    ✕
                                </button>
                            )}
                        </div>

                        {/* Date Range Picker */}
                        <div className="flex items-center gap-2">
                            <span className="text-white">DATE RANGE</span>
                            <div className="relative">
                                <DatePicker
                                    selected={startDate}
                                    onChange={date => setStartDate(date)}
                                    selectsStart
                                    startDate={startDate}
                                    endDate={endDate}
                                    placeholderText="YYYY-MM-DD"
                                    className="p-2 rounded pr-8"
                                    dateFormat="yyyy-MM-dd"
                                    showTimeSelect={false}
                                    isClearable={true}
                                />
                            </div>
                            <span className="text-white">TO</span>
                            <div className="relative">
                                <DatePicker
                                    selected={endDate}
                                    onChange={date => setEndDate(date)}
                                    selectsEnd
                                    startDate={startDate}
                                    endDate={endDate}
                                    minDate={startDate}
                                    placeholderText="YYYY-MM-DD"
                                    className="p-2 rounded pr-8"
                                    dateFormat="yyyy-MM-dd"
                                    showTimeSelect={false}
                                    isClearable={true}
                                />
                            </div>
                            {(startDate || endDate) && (
                                <button
                                    onClick={() => {
                                        setStartDate(null);
                                        setEndDate(null);
                                    }}
                                    className="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded text-sm"
                                    title="Clear date range"
                                >
                                    Clear Dates
                                </button>
                            )}
                        </div>

                        {/* Status Filter */}
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="p-2 rounded bg-white text-black"
                        >
                            <option value="ALL">ALL</option>
                            <option value="GOOD">GOOD</option>
                            <option value="NOT GOOD">NOT GOOD</option>
                        </select>
                    </div>

                    {/* Results Table */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-white">
                            <thead>
                                <tr className="bg-[#1e3a8a]">
                                    <th className="p-3 text-center">NO</th>
                                    <th className="p-3 text-center">JOB ORDER</th>
                                    <th className="p-3 text-center">PART NO</th>
                                    <th className="p-3 text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredResults.map((result, index) => (
                                    <tr key={index} className="border-b border-gray-700">
                                        <td className="p-3 text-center">{index + 1}</td>
                                        <td className="p-3 text-center">{result.joborder}</td>
                                        <td className="p-3 text-center">{result.partno}</td>
                                        <td className="p-3 text-center">
                                            <div className="flex justify-center">
                                                <div className={`w-4 h-4 rounded-full ${
                                                    result.status === 'match' 
                                                        ? 'bg-green-500' 
                                                        : 'bg-red-500'
                                                }`}></div>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div className="mt-6 px-4">
                        <Report 
                            startDate={startDate ? startDate.toISOString().split('T')[0] : null}
                            endDate={endDate ? endDate.toISOString().split('T')[0] : null}
                            hideDownload={true}
                        />
                    </div>
                </div>
            </div>
        </Layout>
    );
}
