import React, { useState, useEffect, useRef } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import axios from 'axios';
// PDF generation will be handled with dynamic imports
import Layout from '@/Layouts/Layout';
import { Home06Icon, Download05Icon } from 'hugeicons-react';
import { CalendarIcon, MagnifyingGlassIcon } from '@heroicons/react/24/outline';
import html2canvas from 'html2canvas';

export default function ProductionReport() {
    const { auth } = usePage().props;
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [reportData, setReportData] = useState({ partRecognition: [], qualityInspection: [] });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [debugInfo, setDebugInfo] = useState({});
    const [pdfLoading, setPdfLoading] = useState(false);
    const reportRef = useRef(null);
    const [dateRange, setDateRange] = useState({
        startDate: new Date().toISOString().split('T')[0],
        endDate: new Date().toISOString().split('T')[0]
    });
    
    // State for reports and current report index
    const [currentReportIndex, setCurrentReportIndex] = useState(0);
    const [reports, setReports] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [searchType, setSearchType] = useState('partNumber'); // 'partNumber', 'jobOrder', or 'trackId'
    const [showDatePicker, setShowDatePicker] = useState(false);
    
    // Format date to YYYY-MM-DD
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toISOString().split('T')[0];
    };

    // Fetch report data from API
    const fetchReportData = async () => {
        setLoading(true);
        setError('');
        try {
            const response = await axios.get('/api/actual-part/report', {
                params: {
                    start_date: dateRange.startDate,
                    end_date: dateRange.endDate,
                    include_quality: true
                }
            });

            if (response.data.status === 'success') {
                setReports(response.data.data);
                if (response.data.data.length > 0) {
                    setCurrentReportIndex(0);
                }
            } else {
                setError('Failed to load report data');
            }
        } catch (err) {
            console.error('Error fetching report data:', err);
            setError('An error occurred while fetching the report data');
        } finally {
            setLoading(false);
        }
    };

    // Fetch data when component mounts or date range changes
    useEffect(() => {
        fetchReportData();
    }, [dateRange]);
    
    // Update the report data when the current report index changes
    useEffect(() => {
        if (filteredReports.length > 0 && currentReportIndex < filteredReports.length) {
            const currentReport = filteredReports[currentReportIndex];
            // Update any derived state here if needed
        }
    }, [currentReportIndex, reports]);

    // Function to crop whitespace from canvas with improved precision
    const cropCanvasWhitespace = (canvas) => {
        const ctx = canvas.getContext('2d');
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;
        
        let minX = canvas.width;
        let maxX = 0;
        let minY = canvas.height;
        let maxY = 0;
        
        // More aggressive detection of non-white pixels
        for (let y = 0; y < canvas.height; y++) {
            for (let x = 0; x < canvas.width; x++) {
                const idx = (y * canvas.width + x) * 4;
                const r = data[idx];
                const g = data[idx + 1];
                const b = data[idx + 2];
                const a = data[idx + 3];
                
                // More aggressive detection - catch any non-pure-white pixels
                if (!(r >= 248 && g >= 248 && b >= 248) || a > 5) {
                    minX = Math.min(minX, x);
                    maxX = Math.max(maxX, x);
                    minY = Math.min(minY, y);
                    maxY = Math.max(maxY, y);
                }
            }
        }
        
        // Use minimal padding to maximize content area
        const padding = 5;
        minX = Math.max(0, minX - padding);
        maxX = Math.min(canvas.width - 1, maxX + padding);
        minY = Math.max(0, minY - padding);
        maxY = Math.min(canvas.height - 1, maxY + padding);
        
        // Ensure we found actual content
        if (minX >= maxX || minY >= maxY) {
            // Fallback: return original canvas if no content detected
            return canvas;
        }
        
        // Create cropped canvas
        const croppedWidth = maxX - minX + 1;
        const croppedHeight = maxY - minY + 1;
        
        const croppedCanvas = document.createElement('canvas');
        croppedCanvas.width = croppedWidth;
        croppedCanvas.height = croppedHeight;
        const croppedCtx = croppedCanvas.getContext('2d');
        
        // Fill with white background first
        croppedCtx.fillStyle = '#ffffff';
        croppedCtx.fillRect(0, 0, croppedWidth, croppedHeight);
        
        // Copy the cropped region
        croppedCtx.drawImage(
            canvas,
            minX, minY, croppedWidth, croppedHeight,
            0, 0, croppedWidth, croppedHeight
        );
        
        return croppedCanvas;
    };

    const exportToPDF = async () => {
        if (pdfLoading) return; // Prevent multiple clicks

        setPdfLoading(true);
        setError('');

        try {
            const { jsPDF } = await import('jspdf');
            const reportElement = reportRef.current;

            if (!reportElement) {
                throw new Error('Report element not found.');
            }

            // Create a temporary style element to override problematic colors
            const tempStyle = document.createElement('style');
            tempStyle.id = 'pdf-export-override';
            tempStyle.textContent = `
                /* Target specific DaisyUI and component classes */
                .bg-base-200 { background-color: rgb(30, 58, 138) !important; } /* Dark blue header */
                .bg-base-100 { background-color: rgb(248, 250, 252) !important; } /* Light background */
                .bg-white { background-color: rgb(255, 255, 255) !important; }
                
                /* Header styling */
                .text-white { color: rgb(255, 255, 255) !important; }
                
                /* Table and section styling */
                .bg-gray-100 { background-color: rgb(243, 244, 246) !important; }
                .bg-gray-200 { background-color: rgb(229, 231, 235) !important; }
                .bg-gray-50 { background-color: rgb(249, 250, 251) !important; }
                .text-gray-700 { color: rgb(55, 65, 81) !important; }
                .text-gray-800 { color: rgb(31, 41, 55) !important; }
                
                /* Borders */
                .border-gray-200 { border-color: rgb(229, 231, 235) !important; }
                .border-gray-300 { border-color: rgb(209, 213, 219) !important; }
                
                /* Button colors */
                .bg-blue-600 { background-color: rgb(37, 99, 235) !important; }
                .bg-blue-700 { background-color: rgb(29, 78, 216) !important; }
                .bg-blue-400 { background-color: rgb(96, 165, 250) !important; }
                
                /* Hover states */
                .hover\:bg-gray-50:hover { background-color: rgb(249, 250, 251) !important; }
                .hover\:bg-blue-700:hover { background-color: rgb(29, 78, 216) !important; }
            `;
            document.head.appendChild(tempStyle);

            let canvas;
            
            try {
                // Use html2canvas with direct content capture and optimized settings
                canvas = await html2canvas(reportElement, {
                    scale: 2,
                    allowTaint: true,
                    useCORS: false,
                    backgroundColor: '#ffffff',
                    // Use natural content dimensions
                    width: null,
                    height: null,
                    // Optimize for content capture
                    scrollX: 0,
                    scrollY: 0,
                    onclone: (clonedDoc) => {
                        // Ensure the temporary styles are applied in the cloned document
                        const clonedStyle = clonedDoc.createElement('style');
                        clonedStyle.textContent = tempStyle.textContent;
                        clonedDoc.head.appendChild(clonedStyle);
                        
                        // Remove data-theme to prevent DaisyUI interference
                        const themedElements = clonedDoc.querySelectorAll('[data-theme]');
                        themedElements.forEach(el => el.removeAttribute('data-theme'));
                        
                        // Optimize the cloned element for tight content capture
                        const clonedReportElement = clonedDoc.querySelector('[data-pdf-content]') || clonedDoc.body;
                        if (clonedReportElement) {
                            // Remove all margins and padding
                            clonedReportElement.style.margin = '0';
                            clonedReportElement.style.padding = '0';
                            clonedReportElement.style.width = 'fit-content';
                            clonedReportElement.style.maxWidth = 'none';
                            clonedReportElement.style.minWidth = '0';
                            clonedReportElement.style.display = 'block';
                            clonedReportElement.style.overflow = 'visible';
                            
                            // Remove any container constraints that might add whitespace
                            const parentContainer = clonedReportElement.parentElement;
                            if (parentContainer) {
                                parentContainer.style.width = 'fit-content';
                                parentContainer.style.maxWidth = 'none';
                                parentContainer.style.padding = '0';
                                parentContainer.style.margin = '0';
                            }
                        }
                    }
                });
            } catch (error) {
                console.warn('HTML2Canvas failed, trying with minimal options:', error);
                
                // Fallback attempt with minimal options
                canvas = await html2canvas(reportElement, {
                    scale: 1.5,
                    allowTaint: true,
                    useCORS: false,
                    backgroundColor: '#ffffff',
                    logging: false,
                    onclone: (clonedDoc) => {
                        // Apply basic color overrides
                        const clonedStyle = clonedDoc.createElement('style');
                        clonedStyle.textContent = `
                            * { color: rgb(55, 65, 81) !important; background-color: transparent !important; }
                            .bg-blue-600, .bg-blue-700 { background-color: rgb(29, 78, 216) !important; }
                            .bg-gray-100, .bg-gray-200 { background-color: rgb(243, 244, 246) !important; }
                            .text-white { color: rgb(255, 255, 255) !important; }
                        `;
                        clonedDoc.head.appendChild(clonedStyle);
                        
                        // Remove problematic attributes
                        const themedElements = clonedDoc.querySelectorAll('[data-theme]');
                        themedElements.forEach(el => el.removeAttribute('data-theme'));
                    }
                });
            } finally {
                // Always remove the temporary style element
                const tempStyleElement = document.getElementById('pdf-export-override');
                if (tempStyleElement) {
                    tempStyleElement.remove();
                }
            }

            // Crop whitespace from the canvas
            const croppedCanvas = cropCanvasWhitespace(canvas);
            const imgData = croppedCanvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'pt',
                format: 'a4',
            });

            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            const canvasWidth = croppedCanvas.width;
            const canvasHeight = croppedCanvas.height;
            const ratio = canvasWidth / canvasHeight;
            
            // Always scale to fill the entire PDF width (stretch if necessary)
            const finalWidth = pdfWidth;
            const finalHeight = finalWidth / ratio;
            
            // If height exceeds page, scale down proportionally but maintain full width
            if (finalHeight > pdfHeight) {
                const scaleFactor = pdfHeight / finalHeight;
                const scaledHeight = pdfHeight;
                const scaledWidth = finalWidth * scaleFactor;
                
                // Still try to fill width as much as possible
                if (scaledWidth < pdfWidth) {
                    // Stretch to fill full width
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, scaledHeight);
                } else {
                    // Use proportional scaling
                    pdf.addImage(imgData, 'PNG', 0, 0, scaledWidth, scaledHeight);
                }
            } else {
                // Content fits - use full width and center vertically
                const yOffset = (pdfHeight - finalHeight) / 2;
                pdf.addImage(imgData, 'PNG', 0, yOffset, finalWidth, finalHeight);
            }

            const currentReport = filteredReports[currentReportIndex];
            const filename = `Inspection_Report_${currentReport.partNumber || 'report'}_${new Date().toISOString().split('T')[0]}.pdf`;
            pdf.save(filename);

        } catch (error) {
            console.error('Error in exportToPDF:', error);
            setError(`Failed to generate PDF: ${error.message || 'Unknown error'}`);
            alert(`Error generating PDF: ${error.message || 'Unknown error'}. Please try again.`);
        } finally {
            setPdfLoading(false);
        }
    };
    
    useEffect(() => {
        let isMounted = true;
        
        const fetchData = async () => {
            try {
                setLoading(true);
                setError(null);
                console.log('Fetching report data...');
                console.log('Date range:', dateRange);
                
                const response = await axios.get(route('production.report.data'), {
                    params: {
                        start_date: dateRange.startDate,
                        end_date: dateRange.endDate
                    }
                });
                
                console.log('API Response:', response);
                
                if (!response.data || !Array.isArray(response.data)) {
                    throw new Error('Invalid response format: expected an array');
                }
                
                if (isMounted) {
                    setReportData(response.data);
                    setDebugInfo({
                        lastFetched: new Date().toISOString(),
                        recordCount: response.data.length,
                        hasPartRecognition: response.data.some(item => item.partRecognition?.length > 0),
                        hasQualityInspection: response.data.some(item => item.qualityInspection?.length > 0)
                    });
                    setLoading(false);
                    
                    if (response.data.length === 0) {
                        console.warn('No data found for the selected date range');
                    }
                }
            } catch (error) {
                console.error('Error fetching report data:', error);
                if (isMounted) {
                    setError({
                        message: error.message,
                        response: error.response?.data,
                        status: error.response?.status
                    });
                    setLoading(false);
                }
            }
        };
        
        fetchData();
        
        return () => {
            isMounted = false;
        };
    }, [dateRange.startDate, dateRange.endDate]);
    
    // Filter reports based on search term and type
    const filteredReports = reports.filter(report => {
        if (!searchTerm.trim()) return true;
        
        const cleanSearchTerm = searchTerm.toLowerCase().trim().replace(/-/g, '');
        
        if (searchType === 'partNumber') {
            const partNumberItem = report.partRecognition?.find(item => item.inspection === 'Part Number');
            const partNumber = partNumberItem ? String(partNumberItem.output || '') : '';
            const cleanPartNumber = partNumber.toLowerCase().replace(/\s/g, '');
            return cleanPartNumber.includes(cleanSearchTerm);
        } else if (searchType === 'jobOrder') {
            const jobOrderItem = report.partRecognition?.find(item => item.inspection === 'Job Order');
            const jobOrder = jobOrderItem ? String(jobOrderItem.output || '') : '';
            const cleanJobOrder = jobOrder.toLowerCase().replace(/\s/g, '');
            return cleanJobOrder.includes(cleanSearchTerm);
        } else if (searchType === 'trackId') {
            const trackIdItem = report.partRecognition?.find(item => 
                item.inspection === 'Track Identification' || 
                item.inspection === 'Track ID' ||
                item.inspection === 'Track ID:'
            );
            if (!trackIdItem) return false;
            
            const trackId = String(trackIdItem.output || '').trim();
            const searchId = searchTerm.trim();
            
            // Log for debugging
            console.log('Comparing track IDs:', { 
                trackId, 
                searchId, 
                match: trackId === searchId,
                reportId: report.id
            });
            
            return trackId === searchId;
        }
        return true;
    });

    return (
        <Layout>
            <Head title="Production Report" />
            <div className="py-6 bg-base-100 min-h-screen">
                {/* Debug Info - Only show in development */}
                {process.env.NODE_ENV === 'development' && (
                    <div className="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div className="flex">
                            <div className="flex-shrink-0">
                                <svg className="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div className="ml-3">
                                <h3 className="text-sm font-medium text-yellow-800">Debug Information</h3>
                                <div className="mt-2 text-sm text-yellow-700">
                                    <pre className="whitespace-pre-wrap text-xs">
                                        {JSON.stringify({
                                            loading,
                                            reportCount: reportData?.length || 0,
                                            currentReportIndex,
                                            debugInfo,
                                            error: error ? {
                                                message: error.message,
                                                status: error.status
                                            } : null
                                        }, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
                
                <div className="max-w-6xl mx-auto px-4">
                    <div className="flex flex-col gap-4 mb-6">
                        {/* Search, Date Range Picker and Export Button */}
                        <div className="flex flex-col md:flex-row gap-4 bg-white p-4 rounded-lg shadow items-center">
                            {/* Date Range Picker */}
                            <div className="relative">
                                <button
                                    onClick={() => setShowDatePicker(!showDatePicker)}
                                    className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors w-full md:w-auto justify-center"
                                >
                                    <CalendarIcon className="w-5 h-5" />
                                    {dateRange.startDate === dateRange.endDate 
                                        ? formatDate(dateRange.startDate)
                                        : `${formatDate(dateRange.startDate)} - ${formatDate(dateRange.endDate)}`
                                    }
                                </button>
                                
                                {showDatePicker && (
                                    <div className="absolute z-10 mt-2 bg-white border rounded-lg shadow-lg p-4 w-full md:w-96">
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                                <input
                                                    type="date"
                                                    value={dateRange.startDate}
                                                    onChange={(e) => setDateRange(prev => ({...prev, startDate: e.target.value}))}
                                                    className="w-full p-2 border rounded"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                                <input
                                                    type="date"
                                                    value={dateRange.endDate}
                                                    onChange={(e) => setDateRange(prev => ({...prev, endDate: e.target.value}))}
                                                    className="w-full p-2 border rounded"
                                                />
                                            </div>
                                        </div>
                                        <div className="mt-4 flex justify-end gap-2">
                                            <button
                                                onClick={() => setShowDatePicker(false)}
                                                className="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                onClick={() => {
                                                    fetchReportData();
                                                    setShowDatePicker(false);
                                                }}
                                                className="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                                            >
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Search Bar */}
                            <div className="relative flex-1 flex gap-2">
                                <div className="relative">
                                    <select
                                        value={searchType}
                                        onChange={(e) => setSearchType(e.target.value)}
                                        className="h-full pl-3 pr-8 py-2 border rounded-lg bg-blue-600 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                    >
                                        <option value="partNumber">Part Number</option>
                                        <option value="jobOrder">Job Order</option>
                                        <option value="trackId">Track ID</option>
                                    </select>
                                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg className="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div className="relative flex-1">
                                    <input
                                        type="text"
                                        placeholder={`Search by ${
                                            searchType === 'partNumber' ? 'part number' : 
                                            searchType === 'jobOrder' ? 'job order' : 'track ID'
                                        }...`}
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        className="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                </div>
                            </div>
                            
                            {/* Export Button */}
                            <div className="w-full md:w-auto">
                                <button
                                    onClick={exportToPDF}
                                    disabled={pdfLoading || reports.length === 0}
                                    className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 w-full justify-center"
                                >
                                    <Download05Icon className="w-5 h-5" />
                                    {pdfLoading ? 'Generating...' : 'Export PDF'}
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Report content - this will be captured in PDF */}
                    <div ref={reportRef} data-pdf-content className="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-300">
                        {/* Header with logo and title - without export button */}
                        <div className="flex items-center justify-between p-4 border-b border-gray-200 bg-base-200">
                            <img src="/img/CTRM.png" alt="Logo" className="h-12" />
                            <div className="flex flex-col items-center">
                                <h1 className="text-xl font-bold text-center text-white tracking-wide uppercase">
                                    IN-PROCESS INSPECTION FORM
                                </h1>
                                <div className="flex items-center justify-center mt-2 space-x-4">
                                    <button 
                                        onClick={() => setCurrentReportIndex(prev => Math.max(0, prev - 1))}
                                        disabled={currentReportIndex === 0}
                                        className="p-1 text-white hover:bg-blue-700 rounded-full disabled:opacity-50 disabled:cursor-not-allowed"
                                        aria-label="Previous report"
                                    >
                                        <span className="text-2xl font-bold">&#8592;</span>
                                    </button>
                                    <span className="text-white text-sm font-medium">
                                        Report {filteredReports.length > 0 ? currentReportIndex + 1 : 0} of {filteredReports.length}
                                    </span>
                                    <button 
                                        onClick={() => setCurrentReportIndex(prev => Math.min(filteredReports.length - 1, prev + 1))}
                                        disabled={currentReportIndex === filteredReports.length - 1 || filteredReports.length === 0}
                                        className="p-1 text-white hover:bg-blue-700 rounded-full disabled:opacity-50 disabled:cursor-not-allowed"
                                        aria-label="Next report"
                                    >
                                        <span className="text-2xl font-bold">&#8594;</span>
                                    </button>
                                </div>
                            </div>
                            {/* Empty space where export button was */}
                            <div className="w-24"></div>
                        </div>
                        
                        <div className="p-4">
                            {/* Job Card Scan - Commented out as per request
                            <h2 className="text-lg font-bold mb-2 text-gray-700">Job Card Scan</h2>
                            <table className="w-full border border-gray-200 text-sm mb-4">
                                <tbody>
                                    {filteredReports[currentReportIndex]?.jobCardScan?.map((item, idx) => (
                                        <tr key={idx} className="hover:bg-gray-50">
                                            <td className="border border-gray-200 p-2 font-semibold text-gray-700 bg-gray-50 w-1/4">
                                                {item.inspection}:
                                            </td>
                                            <td className="border border-gray-200 p-2 text-gray-700 font-mono">
                                                {item.output || 'N/A'}
                                                {process.env.NODE_ENV === 'development' && item.inspection === 'Job Order' && (
                                                    <span className="text-xs text-gray-500 ml-2">
                                                        (ID: {filteredReports[currentReportIndex]?.id || 'N/A'})
                                                    </span>
                                                )}
                                                {item.inspection === 'Part Number' && filteredReports[currentReportIndex]?.track && (
                                                    <span className="text-xs text-gray-500 ml-2">
                                                        (Track: {filteredReports[currentReportIndex]?.track})
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            */}

                            {/* Part Recognition */}
                            <h2 className="text-lg font-bold mb-2 text-gray-700">Part Recognition</h2>
                            <table className="w-full border border-gray-200 text-sm mb-4">
                                <thead>
                                    <tr className="bg-gray-100">
                                        <th className="border border-gray-200 p-2 font-medium text-gray-700 text-center">No.</th>
                                        <th className="border border-gray-200 p-2 font-medium text-gray-700 text-left">Inspection</th>
                                        <th className="border border-gray-200 p-2 font-medium text-gray-700 text-center">EID Output</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredReports[currentReportIndex]?.partRecognition?.map((item, idx) => (
                                        <tr key={idx} className="hover:bg-gray-50">
                                            <td className="border border-gray-200 p-2 text-gray-700 text-center">{item.no}</td>
                                            <td className="border border-gray-200 p-2 text-gray-700">{item.inspection}</td>
                                            <td className="border border-gray-200 p-2 text-gray-700 text-center">
                                                {item.inspection === 'Track Identification' ? (
                                                    <span className="font-mono bg-yellow-50 px-2 py-1 rounded">
                                                        {item.output}
                                                    </span>
                                                ) : (
                                                    item.output
                                                )}
                                            </td>
                                        </tr>
                                    )) || (
                                        <tr>
                                            <td colSpan="3" className="text-center p-4 text-gray-500">
                                                No part recognition data available
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>

                            {/* Quality Inspection */}
                            <h2 className="text-lg font-bold mb-2 text-gray-700">Quality Inspection</h2>
                            <div className="overflow-x-auto">
                                <table className="w-full border border-gray-200 text-sm">
                                    <thead>
                                        <tr className="bg-gray-100">
                                            <th className="border border-gray-200 p-2 font-medium text-gray-700 text-center">No.</th>
                                            <th className="border border-gray-200 p-2 font-medium text-gray-700 text-left">Inspection</th>
                                            <th className="border border-gray-200 p-2 font-medium text-gray-700 text-center">EID Output</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {filteredReports[currentReportIndex]?.qualityInspection?.map((item, idx) => (
                                            <tr key={idx} className="hover:bg-gray-50">
                                                <td className="border border-gray-200 p-2 text-gray-700 text-center">{item.no}</td>
                                                <td className="border border-gray-200 p-2 text-gray-700">{item.inspection}</td>
                                                <td className="border border-gray-200 p-2 text-gray-700 text-center">{item.output}</td>
                                            </tr>
                                        )) || (
                                            <tr>
                                                <td colSpan="3" className="text-center p-4 text-gray-500">
                                                    No quality inspection data available
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
