import React, { useState } from "react";
import axios from "axios";
import * as XLSX from 'xlsx';

const FileUpload = ({ onSuccess }) => {
  const [uploading, setUploading] = useState(false);
  const [message, setMessage] = useState("");

  const filterExcelData = (data) => {
    // Filter the data based on the description column (column O) containing 'track rib'
    return data.filter(row => {
      const description = String(row['O'] || '').toLowerCase();
      return description.includes('track rib');
    }).map(row => ({
      joborder: row['A'], // Column A: Order
      partno: row['B']    // Column B: Material
      // Description is filtered out as per requirement
    }));
  };

  const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    try {
      setUploading(true);
      setMessage("");

      // Read the Excel file
      const data = await file.arrayBuffer();
      const workbook = XLSX.read(data);
      const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
      
      // Convert to array of objects with column letters as keys
      const headers = jsonData[0];
      const rows = jsonData.slice(1);
      const formattedData = rows.map(row => {
        const obj = {};
        row.forEach((cell, i) => {
          if (i < 26) { // Only handle up to column Z for simplicity
            const colLetter = String.fromCharCode(65 + i); // 65 is 'A' in ASCII
            obj[colLetter] = cell;
          }
        });
        return obj;
      });

      // Filter the data
      const filteredData = filterExcelData(formattedData);
      
      if (filteredData.length === 0) {
        throw new Error("No matching data found with the specified descriptions.");
      }

      // Send filtered data to the backend
      const response = await axios.post("/upload-sap", {
        data: filteredData,
        filename: file.name
      }, {
        headers: {
          'Content-Type': 'application/json',
        },
      });

      setMessage(`${filteredData.length} matching records uploaded successfully!`);
      if (onSuccess) {
        onSuccess();
      }
    } catch (error) {
      setMessage(error.response?.data?.message || error.message || "Failed to process file.");
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="p-4">
      <div className="flex flex-col items-center gap-4">
        <input
          type="file"
          accept=".xlsx,.xls"
          onChange={handleFileChange}
          disabled={uploading}
          className="file-input file-input-bordered w-full max-w-xs"
        />
        <p className="text-sm text-gray-400 mt-2">
          Only Excel files (.xlsx, .xls) are accepted. The file will be filtered for specific part descriptions.
        </p>
        {uploading && (
          <div className="text-blue-600">Uploading...</div>
        )}
        {message && (
          <div className={`text-${message.includes('success') ? 'green' : 'red'}-600`}>
            {message}
          </div>
        )}
      </div>
    </div>
  );
};

export default FileUpload;
