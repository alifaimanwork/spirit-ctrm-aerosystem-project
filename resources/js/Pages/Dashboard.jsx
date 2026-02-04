import { useState } from 'react';
import { Head } from "@inertiajs/react";
import Layout from "@/Layouts/Layout";
import FileUpload from '@/Components/FileUpload';

export default function Dashboard({ sapData = [] }) {
    return (
        <Layout>
            <Head title="Production List" />
            
            <div className="min-h-screen bg-[#1E1E1E] p-6">
                {/* Main Content Area */}
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-bold text-white">PRODUCTION LIST</h1>
                    <button 
                        className="bg-[#1e3a8a] text-white px-4 py-2 rounded-lg flex items-center gap-2"
                        onClick={() => document.getElementById('modal_upload').showModal()}
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        ADD NEW PRODUCTION
                    </button>
                </div>

                {/* SAP Table Container */}
                <div className="overflow-hidden bg-[#0A0A29] shadow-sm rounded-lg">
                    <div className="bg-[#1e3a8a] text-white p-3">
                        SAP
                    </div>
                    
                    {sapData && sapData.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">NO</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">JOB ORDER</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">PART NUMBER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {sapData.map((item, index) => (
                                        <tr key={index}>
                                            <td className="p-3 text-white border border-gray-700 text-center">{index + 1}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">{item.joborder}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">{item.partno}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="flex justify-center items-center h-64 bg-[#0A0A29] text-white text-3xl font-bold">
                            NO DATA
                        </div>
                    )}
                </div>

                {/* Upload Modal */}
                <dialog id="modal_upload" className="modal">
                    <div className="modal-box bg-[#1A1A2E] text-white">
                        <h3 className="font-bold text-lg text-center mb-4">Add New Production</h3>
                        <FileUpload onSuccess={() => {
                            document.getElementById('modal_upload').close();
                            window.location.reload();
                        }} />
                        <div className="modal-action">
                            <form method="dialog">
                                <button className="btn bg-[#1e3a8a] text-white hover:bg-[#2e4a9a]">Close</button>
                            </form>
                        </div>
                    </div>
                </dialog>
            </div>
        </Layout>
    );
}
