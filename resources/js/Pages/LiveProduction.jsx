import { useState, useEffect, useRef, useCallback } from 'react';
import axios from 'axios';
import { Head, usePage } from '@inertiajs/react';
import Layout from "@/Layouts/Layout";
import { ChartBarIcon, CloudIcon, CubeIcon, PlayIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/react/24/solid';

export default function LiveProduction() {
    const [isRunning, setIsRunning] = useState(false);
    const isRunningRef = useRef(false);
    const [jobCardData, setJobCardData] = useState(null);
    const [sapData, setSapData] = useState(null);
    const [actualPartData, setActualPartData] = useState(null);
    
    // State management
    const [status, setStatus] = useState('STOPPED');
    const [error, setError] = useState(null);
    const [compareJobCardSap, setCompareJobCardSap] = useState({ jobOrder: false, partNo: false });
    const [compareSapActual, setCompareSapActual] = useState({ jobOrder: false, partNo: false });
    const [isProcessing, setIsProcessing] = useState(false);
    
    const processingRef = useRef(false);
    const intervalRef = useRef(null);

    const fetchJobCardData = useCallback(async () => {
        try {
            setStatus('FETCHING NEXT JOB CARD...');
            
            // Clear previous comparison data
            setJobCardData(null);
            setSapData(null);
            setActualPartData(null);
            setCompareJobCardSap({ jobOrder: false, partNo: false });
            setCompareSapActual({ jobOrder: false, partNo: false });
            
            // First, try to get the next uncompared record from hub_data
            const hubResponse = await axios.get('/api/hub-data/next-unprocessed', {
                validateStatus: (status) => status < 500 // Don't throw on 404
            });

            if (hubResponse.data?.status === 'success' && hubResponse.data?.data) {
                const { id, job_order: jobOrder, part_number: partNumber, created_at: timestamp } = hubResponse.data.data;
                if (jobOrder && partNumber) {
                    // Normalize the job order and part number for consistent comparison
                    const normalizedJobOrder = normalizeString(jobOrder);
                    const normalizedPartNumber = normalizeString(partNumber);
                    
                    const newJobCardData = { 
                        id, // Include the ID for fetching corresponding actual part data
                        jobOrder: normalizedJobOrder,
                        partNumber: normalizedPartNumber,
                        originalJobOrder: jobOrder, // Keep original values for display
                        originalPartNumber: partNumber, // Keep original values for display
                        timestamp,
                        source: 'hub_data'
                    };
                    console.log('Fetched job card from hub data:', newJobCardData);
                    setJobCardData(newJobCardData);
                    return newJobCardData;
                }
            } else if (hubResponse.status === 404) {
                // No uncompared data found, return null to trigger standby mode
                console.log('No uncompared data found, going to standby');
                return null;
            }
            
            // If no hub data, check for latest actual part data
            const actualPartResponse = await axios.get('/api/actual-part/latest');
            if (actualPartResponse.data?.status === 'success' && actualPartResponse.data?.data) {
                const { id, job_order: jobOrder, part_number: partNumber, created_at: timestamp } = actualPartResponse.data.data;
                if (jobOrder && partNumber) {
                    // Normalize the job order and part number for consistent comparison
                    const normalizedJobOrder = normalizeString(jobOrder);
                    const normalizedPartNumber = normalizeString(partNumber);
                    
                    const newJobCardData = { 
                        id, // Include the ID for fetching corresponding actual part data
                        jobOrder: normalizedJobOrder,
                        partNumber: normalizedPartNumber,
                        originalJobOrder: jobOrder, // Keep original values for display
                        originalPartNumber: partNumber, // Keep original values for display
                        timestamp,
                        source: 'actual_part'
                    };
                    console.log('Fetched job card from actual-part:', newJobCardData);
                    setJobCardData(newJobCardData);
                    return newJobCardData;
                }
            }
            
            // As a last resort, try to get from job card
            const jobCardResponse = await axios.get('/api/job-card/next-unprocessed');
            if (jobCardResponse.data?.data) {
                const { id, joborder: jobOrder, partno: partNumber, created_at: timestamp } = jobCardResponse.data.data;
                if (jobOrder && partNumber) {
                    // Normalize the job order and part number for consistent comparison
                    const normalizedJobOrder = normalizeString(jobOrder);
                    const normalizedPartNumber = normalizeString(partNumber);
                    
                    const newJobCardData = { 
                        id, // Include the ID for fetching corresponding actual part data
                        jobOrder: normalizedJobOrder,
                        partNumber: normalizedPartNumber,
                        originalJobOrder: jobOrder, // Keep original values for display
                        originalPartNumber: partNumber, // Keep original values for display
                        timestamp,
                        source: 'job_card'
                    };
                    console.log('Fetched job card from job-card:', newJobCardData);
                    setJobCardData(newJobCardData);
                    return newJobCardData;
                }
            }
            
            return null;
        } catch (error) {
            console.error('Error fetching job card data:', error);
            setError('Failed to fetch job card data');
            return null;
        }
    }, []);

    const fetchSapData = useCallback(async (jobOrder, partNumber) => {
        try {
            setStatus('CHECKING SAP DATA...');
            
            // Default response when no matching SAP data is found
            const notFoundResponse = {
                jobOrder: null,
                partNumber: null,
                timestamp: new Date().toISOString(),
                status: 'not_found',
                message: 'No matching SAP data found'
            };
            
            try {
                // Normalize the part number before making the API request
                const normalizedJobOrder = normalizeString(jobOrder);
                const normalizedPartNumber = normalizeString(partNumber);
                
                const response = await axios.get('/api/sap/match', {
                    params: { 
                        job_order: normalizedJobOrder, 
                        part_number: normalizedPartNumber
                    },
                    // Don't throw on 404
                    validateStatus: (status) => status < 500
                });

                if (response.data?.status === 'success' && response.data?.data) {
                    const { job_order, part_number, created_at } = response.data.data;
                    // Normalize the job order and part number for consistent comparison
                    const normalizedJobOrder = normalizeString(job_order);
                    const normalizedPartNumber = normalizeString(part_number);
                    
                    return {
                        jobOrder: normalizedJobOrder,
                        partNumber: normalizedPartNumber,
                        originalJobOrder: job_order, // Keep original values for display
                        originalPartNumber: part_number, // Keep original values for display
                        timestamp: created_at || new Date().toISOString(),
                        status: 'found',
                        source: 'sap'
                    };
                }
                
                // If we get here, no matching data was found
                console.log(`No SAP data found for job order ${jobOrder} and part ${partNumber}`);
                return notFoundResponse;
                
            } catch (error) {
                console.log('Error fetching SAP data, defaulting to not found:', error.message);
                return notFoundResponse;
            }
            // This code is no longer needed as we return early in the function
        } catch (error) {
            console.error('Error fetching SAP data:', error);
            setError('Failed to fetch SAP data');
            return null;
        }
    }, []);

    const searchByJobOrderAndPartNumber = async (jobCard) => {
        if (!jobCard) {
            console.error('No job card provided to searchByJobOrderAndPartNumber');
            return null;
        }

        console.log('Searching by job order and part number...', {
            jobOrder: jobCard.jobOrder,
            partNumber: jobCard.partNumber,
            originalJobOrder: jobCard.originalJobOrder,
            originalPartNumber: jobCard.originalPartNumber
        });
        
        try {
            // Validate required fields
            if (!jobCard.jobOrder || !jobCard.partNumber) {
                console.error('Missing required job card fields');
                return null;
            }

            // Create a version of the part number with hyphens removed
            const normalizedPartNumber = jobCard.partNumber.replace(/[- ]/g, '');
            
            // Create a version of the original part number with hyphens removed
            const normalizedOriginalPartNumber = jobCard.originalPartNumber ? 
                jobCard.originalPartNumber.replace(/[- ]/g, '') : '';
            
            console.log('Searching with part numbers:', {
                partNumber: jobCard.partNumber,
                originalPartNumber: jobCard.originalPartNumber,
                timestamp: new Date().toISOString()
            });

            // First try to get the latest actual part data
            const latestPromise = axios.get('/api/actual-part/latest', {
                params: {
                    job_order: jobCard.jobOrder,
                    part_number: jobCard.partNumber,
                    hub_data_id: jobCard.hub_data_id, // Include hub_data_id in the request
                    original_part_number: jobCard.originalPartNumber, // Include original part number
                    _t: Date.now() // Cache buster
                },
                validateStatus: (status) => status < 500
            })
            .then(response => ({
                ...response,
                trackId: 'latest',
                success: response.data?.status === 'success' && response.data?.data,
                timestamp: response.data?.data?.created_at,
                source: 'actual_part_latest'
            }));

            // Also try to get data by specific call IDs if needed
            const trackPromises = [1, 2, 3, 4].map(trackId => {
                try {
                    const url = `/api/actual-part/${trackId}`;
                    const params = {
                        job_order: jobCard.jobOrder,
                        part_number: jobCard.partNumber,
                        hub_data_id: jobCard.hub_data_id, // Include hub_data_id in the request
                        original_part_number: jobCard.originalPartNumber, // Include original part number
                        _t: Date.now() // Cache buster
                    };

                    console.log(`Calling ${url} with params:`, params);
                    
                    return axios.get(url, {
                        params,
                        validateStatus: (status) => status < 500
                    })
                    .then(response => ({
                        ...response,
                        trackId,
                        success: response.data?.status === 'success' && response.data?.data,
                        timestamp: response.data?.data?.created_at,
                        source: 'call_id_data'
                    }));
                } catch (error) {
                    console.error(`Error in track ${trackId} request setup:`, error);
                    return Promise.resolve({ success: false, trackId, error: error.message });
                }
            });

            // Wait for all requests to complete (both latest and specific track IDs)
            const allPromises = [latestPromise, ...trackPromises];
            const trackResponses = await Promise.all(allPromises);
            
            // Filter successful responses and sort by timestamp (newest first)
            const successfulResponses = trackResponses
                .filter(r => r.success && r.data?.data && 
                    (r.data.data.job_order || r.data.data.joborder) && 
                    (r.data.data.part_number || r.data.data.partno))
                .sort((a, b) => new Date(b.timestamp || 0) - new Date(a.timestamp || 0));

            console.log('Track responses (with job order and part number):', trackResponses);

            // If we have a successful response, return the data
            if (successfulResponses.length > 0) {
                                const { data: { data }, trackId } = successfulResponses[0];
                // Handle both formats: job_order/joborder and part_number/partno
                const job_order = data.job_order || data.joborder;
                const part_number = data.part_number || data.partno;
                const { created_at, quality_status, id } = data || {};
                
                return {
                    id,
                    jobOrder: job_order,
                    partNumber: part_number,
                    originalJobOrder: job_order,
                    originalPartNumber: part_number,
                    timestamp: created_at || new Date().toISOString(),
                    quality_status: quality_status || 'available',
                    status: 'found',
                    source: `call_id_${trackId}`,
                    isFallback: false,
                    normalizedJobOrder: normalizeString(job_order),
                    normalizedPartNumber: normalizeString(part_number)
                };
            }
            
            return null;
        } catch (error) {
            console.error('Error in searchByJobOrderAndPartNumber:', error);
            return null;
        }
    };

    const fetchActualPartData = useCallback(async (jobCard, sapData) => {
        try {
            setStatus('GETTING ACTUAL PART DATA...');
            
            // Default response when no actual part data is found
            const notFoundResponse = {
                jobOrder: null,
                partNumber: null,
                timestamp: new Date().toISOString(),
                status: 'not_found',
                message: 'No matching actual part data found',
                quality_status: 'not_available'
            };

            if (!jobCard) {
                console.log('No job card provided for actual part data');
                return notFoundResponse;
            }
            
            // Check if there's no SAP data or if job card and SAP data don't match
            const isMismatch = !sapData || sapData.status === 'not_found' || 
                (jobCard.jobOrder !== sapData.jobOrder || jobCard.partNumber !== sapData.partNumber);
                
            console.log(`Fetching actual part data - Mismatch or no SAP data: ${isMismatch}`, {
                hasSapData: !!sapData,
                sapStatus: sapData?.status,
                jobOrderMatch: sapData ? jobCard.jobOrder === sapData.jobOrder : 'no sap data',
                partNumberMatch: sapData ? jobCard.partNumber === sapData.partNumber : 'no sap data',
                jobCard: {
                    jobOrder: jobCard.jobOrder,
                    partNumber: jobCard.partNumber,
                    originalJobOrder: jobCard.originalJobOrder,
                    originalPartNumber: jobCard.originalPartNumber,
                    hub_data_id: jobCard.hub_data_id
                },
                sapData: sapData ? {
                    jobOrder: sapData.jobOrder,
                    partNumber: sapData.partNumber,
                    originalJobOrder: sapData.originalJobOrder,
                    originalPartNumber: sapData.originalPartNumber
                } : null
            });

            // If this is a match, try to find the actual part using the SAP data's part number
            if (!isMismatch && sapData) {
                console.log('Job card and SAP data match, searching with SAP data:', {
                    jobOrder: sapData.jobOrder,
                    partNumber: sapData.partNumber,
                    originalPartNumber: sapData.originalPartNumber
                });
                
                // Create a copy of the job card with SAP data for the search
                const sapBasedJobCard = {
                    ...jobCard,
                    jobOrder: sapData.jobOrder,
                    partNumber: sapData.partNumber,
                    originalPartNumber: sapData.originalPartNumber || sapData.partNumber
                };
                
                // Try to find using SAP data
                const sapMatch = await searchByJobOrderAndPartNumber(sapBasedJobCard);
                if (sapMatch) {
                    console.log('Found match using SAP data:', sapMatch);
                    return sapMatch;
                }
                
                console.log('No match found using SAP data, falling back to job card data');
            }

            try {
                // First, try to find by hub_data_id if available (for non-matching job cards)
                if (jobCard.hub_data_id) {
                    console.log(`Looking for actual part data using hub_data_id: ${jobCard.hub_data_id}`);
                    
                    const hubDataPromises = [1, 2, 3, 4].map(trackId => 
                        axios.get('/api/actual-part/latest', {
                            params: {
                                track_id: trackId,
                                hub_data_id: jobCard.hub_data_id,
                                // Include job order and part number as fallback
                                job_order: jobCard.jobOrder,
                                part_number: jobCard.partNumber,
                                original_part_number: jobCard.originalPartNumber
                            },
                            validateStatus: (status) => status < 500
                        })
                        .then(response => ({
                            ...response,
                            trackId,
                            success: response.data?.status === 'success' && response.data?.data,
                            timestamp: response.data?.data?.created_at,
                            source: 'hub_data_id'
                        }))
                        .catch(error => {
                            console.error(`Error fetching from track ${trackId} with hub_data_id:`, error);
                            return { success: false, trackId };
                        })
                    );

                    const hubDataResponses = await Promise.all(hubDataPromises);
                    const hubDataMatch = hubDataResponses
                        .filter(r => r.success && r.data?.data)
                        .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))[0];

                    if (hubDataMatch) {
                        const { data: { data }, trackId } = hubDataMatch;
                        const { job_order, part_number, created_at, quality_status, id } = data;
                        
                        const actualPart = {
                            id,
                            jobOrder: job_order,
                            partNumber: part_number,
                            originalJobOrder: job_order,
                            originalPartNumber: part_number,
                            timestamp: created_at || new Date().toISOString(),
                            quality_status: quality_status || 'available',
                            status: 'found',
                            source: `call_id_${trackId}`,
                            isFallback: false, // This is a direct match via hub_data_id
                            normalizedJobOrder: normalizeString(job_order),
                            normalizedPartNumber: normalizeString(part_number)
                        };
                        
                        console.log(`Found actual part via hub_data_id from track ${trackId}:`, actualPart);
                        return actualPart;
                    }
                }

                // If no match by hub_data_id or hub_data_id not available, try by job order and part number
                console.log('Trying to find by job order and part number...', {
                    jobOrder: jobCard.jobOrder,
                    partNumber: jobCard.partNumber,
                    originalJobOrder: jobCard.originalJobOrder,
                    originalPartNumber: jobCard.originalPartNumber
                });
                
                const trackPromises = [1, 2, 3, 4].map(trackId => 
                    axios.get('/api/actual-part/latest', {
                        params: {
                            track_id: trackId,
                            job_order: jobCard.jobOrder,
                            part_number: jobCard.partNumber,
                            original_part_number: jobCard.originalPartNumber,
                            hub_data_id: jobCard.hub_data_id
                        },
                        validateStatus: (status) => status < 500
                    })
                    .then(response => ({
                        ...response,
                        trackId,
                        success: response.data?.status === 'success' && response.data?.data,
                        timestamp: response.data?.data?.created_at,
                        source: 'job_order_part_number'
                    }))
                    .catch(error => {
                        console.error(`Error fetching actual part data for track ${trackId}:`, error);
                        return { success: false, trackId };
                    })
                );

                // Wait for all track requests to complete
                const trackResponses = await Promise.all(trackPromises);
                
                // Filter successful responses and sort by timestamp (newest first)
                let successfulResponses = trackResponses
                    .filter(r => r.success && r.data?.data?.job_order && r.data?.data?.part_number)
                    .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

                console.log('Track responses (with job order and part number):', trackResponses);

                // If no matches found with job order and part number, try to find any recent record
                if (successfulResponses.length === 0) {
                    console.log('No matches with job order and part number, trying to find any recent record...');
                    
                    // Get the most recent record from any track
                    const recentPromises = [1, 2, 3, 4].map(trackId => 
                        axios.get('/api/actual-part/latest', {
                            params: {
                                track_id: trackId,
                                hub_data_id: jobCard.hub_data_id
                            },
                            validateStatus: (status) => status < 500
                        })
                        .then(response => ({
                            ...response,
                            trackId,
                            success: response.data?.status === 'success' && response.data?.data,
                            timestamp: response.data?.data?.created_at,
                            source: 'recent_record'
                        }))
                        .catch(error => {
                            console.error(`Error fetching recent record from track ${trackId}:`, error);
                            return { success: false, trackId };
                        })
                    );

                    const recentResponses = await Promise.all(recentPromises);
                    successfulResponses = recentResponses
                        .filter(r => r.success && r.data?.data?.job_order && r.data?.data?.part_number)
                        .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

                    console.log('Recent records from all tracks:', successfulResponses);
                }

                // Use the most recent record if available
                if (successfulResponses.length > 0) {
                    const { data: { data }, trackId } = successfulResponses[0];
                    const { job_order, part_number, created_at, quality_status, id } = data;
                    
                    const actualPart = {
                        id,
                        jobOrder: job_order,
                        partNumber: part_number,
                        originalJobOrder: job_order,
                        originalPartNumber: part_number,
                        timestamp: created_at || new Date().toISOString(),
                        quality_status: quality_status || 'available',
                        status: 'found',
                        source: `call_id_${trackId}`,
                        isFallback: !(jobCard.originalJobOrder && job_order.includes(jobCard.originalJobOrder)),
                        normalizedJobOrder: normalizeString(job_order),
                        normalizedPartNumber: normalizeString(part_number)
                    };
                    
                    console.log(`Using actual part from track ${trackId}:`, actualPart);
                    return actualPart;
                }

                console.log('No actual part data available for job card in any track:', jobCard);
                return notFoundResponse;
                
            } catch (error) {
                console.error('Error in fetchActualPartData:', error);
                console.log('Error response:', error.response?.data);
                return notFoundResponse;
            }
        } catch (error) {
            console.error('Error in fetchActualPartData:', error);
        setError(error.response?.data?.message || 'Failed to fetch actual part data');
        return null;
    }
}, []);

// Helper function to normalize strings by removing hyphens and converting to uppercase
const normalizeString = (str) => {
    if (!str) return '';
    return str.toString().replace(/-/g, '').toUpperCase();
};

const saveProcessedData = useCallback(async (jobCard, sap, actualPart) => {
    try {
        setStatus('SAVING DATA...');
        
        // Mark job card as processed if it has an ID
        if (jobCard?.id) {
            try {
                await axios.post(`/api/job-card/${jobCard.id}/mark-processed`);
            } catch (error) {
                console.log('Error marking job card as processed:', error.message);
                // Continue even if marking as processed fails
            }
        }

        // Check if we have the minimum required data (at least job card data)
        if (!jobCard?.jobOrder || !jobCard?.partNumber) {
            console.log('Insufficient job card data to save');
            return false;
        }
        
        // Prepare data with null checks for missing SAP or actual part data
        const sapData = sap?.status === 'found' ? sap : null;
        const actualPartData = actualPart?.status === 'found' ? actualPart : null;

        // Prepare data in the format expected by the server
        const requestData = {
            // Job Card Data (required) - Use original values for display
            job_card_job_order: jobCard.originalJobOrder || jobCard.jobOrder,
            job_card_part_number: jobCard.originalPartNumber || jobCard.partNumber,
            job_card_timestamp: jobCard.timestamp || new Date().toISOString(),
            
            // SAP Data (optional) - Use original values for display
            sap_job_order: (sapData?.originalJobOrder || sapData?.jobOrder) || null,
            sap_part_number: (sapData?.originalPartNumber || sapData?.partNumber) || null,
            sap_timestamp: sapData?.timestamp || null,
            
            // Actual Part Data (optional) - Use original values for display
            actual_part_job_order: (actualPartData?.originalJobOrder || actualPartData?.jobOrder) || null,
            actual_part_number: (actualPartData?.originalPartNumber || actualPartData?.partNumber) || null,
            actual_part_timestamp: actualPartData?.timestamp || null,
            actual_part_quality_status: actualPartData?.quality_status || 'not_available',
            
            // Comparison Results (ignoring hyphens and case)
            job_order_match_jobcard_sap: sapData ? normalizeString(jobCard.jobOrder) === normalizeString(sapData.jobOrder) : false,
            part_number_match_jobcard_sap: sapData ? normalizeString(jobCard.partNumber) === normalizeString(sapData.partNumber) : false,
            job_order_match_sap_actual: (sapData && actualPartData) ? normalizeString(sapData.jobOrder) === normalizeString(actualPartData.jobOrder) : false,
            part_number_match_sap_actual: (sapData && actualPartData) ? normalizeString(sapData.partNumber) === normalizeString(actualPartData.partNumber) : false,
            
            // Status (PASS if all available comparisons match, otherwise FAIL)
            status: (!sapData || !actualPartData) ? 'incomplete' : 
                   (normalizeString(jobCard.jobOrder) === normalizeString(sapData.jobOrder) && 
                    normalizeString(jobCard.partNumber) === normalizeString(sapData.partNumber) &&
                    normalizeString(sapData.jobOrder) === normalizeString(actualPartData.jobOrder) && 
                    normalizeString(sapData.partNumber) === normalizeString(actualPartData.partNumber)) ? 'pass' : 'fail',
            
            // Add metadata
            metadata: {
                processed_at: new Date().toISOString(),
                source: 'live_production'
            }
        };

        // Log the data being sent for debugging
        console.log('Saving processed data:', requestData);

        // Send the request to the server
        try {
            await axios.post('/api/processed-data', requestData);
            console.log('Successfully saved processed data');
            return true;
        } catch (error) {
            console.error('Error saving processed data:', error);
            setError('Failed to save processed data: ' + (error.response?.data?.message || error.message));
            return false;
        }
    } catch (error) {
        console.error('Unexpected error in saveProcessedData:', error);
        setError('An unexpected error occurred while saving data');
        return false;
    }
    }, []);

    const processHubData = useCallback(async () => {
        let hasMore = true;
        let processedCount = 0;
        let attempt = 0;
        const maxAttempts = 5; // Prevent infinite loops
        
        // Keep processing records until there are no more unprocessed records or max attempts reached
        while (hasMore && attempt < maxAttempts) {
            attempt++;
            console.log(`Processing attempt ${attempt}/${maxAttempts}`);
            
            try {
                const response = await axios.post('/api/actual-part/process-unprocessed', {}, {
                    validateStatus: (status) => status < 500
                });
                
                console.log('Process response:', response.data);
                
                if (response.data?.status === 'success') {
                    if (response.data.processed) {
                        processedCount++;
                        // Reset attempt counter since we're making progress
                        attempt = 0;
                        console.log(`Processed record ${processedCount}`);
                    } else {
                        // No more records to process
                        hasMore = false;
                    }
                } else {
                    // No more records to process or error occurred
                    hasMore = false;
                }
            } catch (error) {
                console.error('Error processing hub data:', error);
                return false;
            }
        }
        
        console.log(`Finished processing ${processedCount} records`);
        return processedCount > 0;
    }, []);

    const [isStandby, setIsStandby] = useState(false);
    const [lastProcessedJobCard, setLastProcessedJobCard] = useState(null);
    const standbyTimeoutRef = useRef(null);
    
    // Clear timeout on unmount
    useEffect(() => {
        return () => {
            if (standbyTimeoutRef.current) {
                clearTimeout(standbyTimeoutRef.current);
            }
        };
    }, []);
    
    const checkIfProcessed = useCallback(async (jobCard) => {
        try {
            if (!jobCard) return true;
            
            // Check if this job card was just processed
            if (lastProcessedJobCard && 
                lastProcessedJobCard.jobOrder === jobCard.jobOrder && 
                lastProcessedJobCard.partNumber === jobCard.partNumber) {
                return true;
            }
            
            // Optionally, check the server if this job card was already processed
            const response = await axios.get('/api/processed-data/check', {
                params: {
                    job_order: jobCard.jobOrder,
                    part_number: jobCard.partNumber
                }
            });
            
            return response.data.exists;
        } catch (error) {
            console.error('Error checking if job card was processed:', error);
            return false; // If there's an error, assume it's not processed
        }
    }, [lastProcessedJobCard]);
    
    const runProductionCycle = useCallback(async () => {
        if (!isRunningRef.current || processingRef.current) {
            return;
        }
        
        try {
            processingRef.current = true;
            setIsProcessing(true);
            
            // Process hub data in the background without status updates
            try {
                await processHubData();
            } catch (error) {
                console.log('Background processing of hub data failed, continuing:', error.message);
            }
            
            // Get next job card for comparison
            const jobCard = await fetchJobCardData();
            if (!jobCard) {
                setIsStandby(true);
                
                // Clear any existing timeout to prevent multiple timeouts
                if (standbyTimeoutRef.current) {
                    clearTimeout(standbyTimeoutRef.current);
                }
                
                // Use a single timeout for the next check
                standbyTimeoutRef.current = setTimeout(() => {
                    processingRef.current = false;
                    setIsProcessing(false);
                    runProductionCycle();
                }, 120000); // Check again after 2 minutes (120000 ms)
                return;
            }
            
            // Clear standby status when we have work to do
            if (isStandby) {
                setIsStandby(false);
            }
            
            // Check if this job card was already processed
            const alreadyProcessed = await checkIfProcessed(jobCard);
            if (alreadyProcessed) {
                // Only update status if it's different to prevent unnecessary re-renders
                if (status !== 'JOB CARD ALREADY PROCESSED - SKIPPING') {
                    setStatus('JOB CARD ALREADY PROCESSED - SKIPPING');
                }
                // Skip already processed job card
                processingRef.current = false;
                setIsProcessing(false);
                setTimeout(runProductionCycle, 1000);
                return;
            }
            
            // Get SAP data
            const sap = await fetchSapData(jobCard.jobOrder, jobCard.partNumber);
            if (sap?.status === 'not_found') {
                console.log('No matching SAP data found, continuing with empty SAP data');
            }
            
            // Get actual part data using the job card and SAP data
            const actualPart = await fetchActualPartData(jobCard, sap);
            
            // Compare data
            const jobCardSapMatch = {
                jobOrder: jobCard?.jobOrder && sap?.jobOrder ? 
                    jobCard.jobOrder === sap.jobOrder : false,
                partNo: jobCard?.partNumber && sap?.partNumber ? 
                    jobCard.partNumber === sap.partNumber : false
            };
            
            const sapActualMatch = {
                jobOrder: sap?.jobOrder && actualPart?.jobOrder ? 
                    sap.jobOrder === actualPart.jobOrder : false,
                partNo: sap?.partNumber && actualPart?.partNumber ? 
                    sap.partNumber === actualPart.partNumber : false
            };
            
            console.log('Job Card Data:', jobCard);
            console.log('SAP Data:', sap);
            console.log('Actual Part Data:', actualPart);
            console.log('Job Card vs SAP Match:', jobCardSapMatch);
            console.log('SAP vs Actual Part Match:', sapActualMatch);
            
            // Update state with the fetched data
            setJobCardData(jobCard);
            setSapData(sap);
            setActualPartData(actualPart);
            setCompareJobCardSap(jobCardSapMatch);
            setCompareSapActual(sapActualMatch);
            
            // Save processed data
            const saveSuccess = await saveProcessedData(jobCard, sap, actualPart);
            if (!saveSuccess) {
                throw new Error('Failed to save processed data');
            }
            
            // Update last processed job card
            setLastProcessedJobCard({
                jobOrder: jobCard.jobOrder,
                partNumber: jobCard.partNumber,
                timestamp: new Date().toISOString()
            });
            
            // Keep status as RUNNING during processing
            // The comparison results are shown in the UI components
            
            // Wait for 3 minutes before processing the next job
            await new Promise(resolve => setTimeout(resolve, 180000));
            
        } catch (error) {
            console.error('Error in production cycle:', error);
            setError(error.message || 'An error occurred during production cycle');
            setStatus('ERROR');
            
            // Add a delay before retrying to prevent rapid API calls
            await new Promise(resolve => setTimeout(resolve, 5000));
            
        } finally {
            processingRef.current = false;
            setIsProcessing(false);
            
            // Only continue if still running
            if (isRunningRef.current) {
                // Small delay before next cycle
                setTimeout(runProductionCycle, 2000);
            }
        }
    }, [fetchJobCardData, fetchSapData, fetchActualPartData, saveProcessedData, isStandby, isRunningRef, isProcessing, status, lastProcessedJobCard]);

    const startProduction = useCallback(() => {
        setIsRunning(true);
        isRunningRef.current = true;
        setStatus('RUNNING');
        setError(null);
        
        // Start the production cycle
        runProductionCycle();
        
        // Set up polling for new data
        intervalRef.current = setInterval(() => {
            if (!processingRef.current) {
                runProductionCycle();
            }
        }, 5000);
    }, [runProductionCycle]);

    const stopProduction = useCallback(() => {
        setIsRunning(false);
        isRunningRef.current = false;
        setStatus('STOPPED');
        
        // Clear any running intervals
        if (intervalRef.current) {
            clearInterval(intervalRef.current);
            intervalRef.current = null;
        }
    }, []);

    // Cleanup on unmount
    useEffect(() => {
        return () => {
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
            }
            isRunningRef.current = false;
        };
    }, []);

    return (
        <Layout>
            <Head title="Live Production" />
            
            <div style={{ backgroundColor: '#030318', height: 'calc(100vh - 4rem)', overflow: 'hidden', display: 'flex', flexDirection: 'column', padding: '0.5rem 0' }}>
                <div className="flex flex-col items-center" style={{ height: '100%', gap: '0.75rem' }}>
                    {/* Main content - Three column layout */}
                    <div className="w-11/12 flex flex-col" style={{ height: '50%' }}>
                        <div className="bg-gray-900 bg-opacity-50 rounded-lg p-1 flex flex-col" style={{ height: '100%' }}>
                            {/* Table Header */}
                            <div className="flex items-center justify-between bg-indigo-900 rounded-t-lg h-16 px-6">
                                <div className="flex-1 flex items-center justify-center">
                                    <ChartBarIcon className="h-6 w-6 text-white mr-3" />
                                    <span className="text-white font-bold text-lg">JOB CARD</span>
                                </div>
                                <div className="flex-1 flex items-center justify-center">
                                    <CloudIcon className="h-6 w-6 text-white mr-3" />
                                    <span className="text-white font-bold text-lg">SAP</span>
                                </div>
                                <div className="flex-1 flex items-center justify-center">
                                    <CubeIcon className="h-6 w-6 text-white mr-3" />
                                    <span className="text-white font-bold text-lg">ACTUAL PART</span>
                                </div>
                                <div className="ml-auto">
                                    <button 
                                        onClick={isRunning ? stopProduction : startProduction} 
                                        className={`${isRunning ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'} rounded-full h-14 w-14 flex items-center justify-center shadow-lg transition-transform hover:scale-105`}
                                    >
                                        {isRunning ? (
                                            <div className="w-5 h-5 bg-white rounded-sm"></div>
                                        ) : (
                                            <PlayIcon className="h-8 w-8 text-white ml-1" />
                                        )}
                                    </button>
                                </div>
                            </div>

                            {/* Table Body */}
                            <div className="p-4 text-white space-y-4 overflow-y-auto" style={{ height: 'calc(100% - 4rem)' }}>
                                {error && (
                                    <div className="bg-red-900 text-white p-4 rounded mb-4">
                                        {error}
                                    </div>
                                )}
                                {/* Job Order Row */}
                                <div>
                                    <div className="flex">
                                        <div className="w-1/3 pr-2"><div className="mb-1 text-sm font-medium text-gray-400">JOB ORDER</div></div>
                                        <div className="w-auto px-4"></div> {/* Spacer for icon */}
                                        <div className="w-1/3 px-2"><div className="mb-1 text-sm font-medium text-gray-400">JOB ORDER</div></div>
                                        <div className="w-auto px-4"></div> {/* Spacer for icon */}
                                        <div className="w-1/3 pl-2"><div className="mb-1 text-sm font-medium text-gray-400">JOB ORDER</div></div>
                                    </div>
                                    <div className="flex items-center">
                                        <div className="w-1/3 pr-2">
                                            <div className={`p-3 rounded text-base font-medium h-12 flex items-center ${!jobCardData?.jobOrder ? 'bg-gray-800' : compareJobCardSap.jobOrder ? 'bg-indigo-950' : 'bg-yellow-900'}`}>
                                                {jobCardData?.jobOrder || '...'}
                                                {jobCardData?.jobOrder && !compareJobCardSap.jobOrder && sapData?.jobOrder && (
                                                    <span className="ml-2 text-yellow-400 text-xs">(Mismatch)</span>
                                                )}
                                            </div>
                                        </div>
                                        <div className="w-auto px-4 flex justify-center">
                                            {isRunning && (compareJobCardSap.jobOrder ? <CheckCircleIcon className="w-7 h-7 text-green-500" /> : <XCircleIcon className="w-7 h-7 text-red-500" />)}
                                        </div>
                                        <div className="w-1/3 px-2">
                                            <div className={`p-3 rounded text-base font-medium h-12 flex items-center ${sapData?.jobOrder ? 'bg-indigo-950' : 'bg-gray-800'}`}>
                                                {sapData?.jobOrder || '...'}
                                            </div>
                                        </div>
                                        <div className="w-auto px-4 flex justify-center">
                                            {isRunning && (compareSapActual.jobOrder ? <CheckCircleIcon className="w-7 h-7 text-green-500" /> : <XCircleIcon className="w-7 h-7 text-red-500" />)}
                                        </div>
                                        <div className="w-1/3 pl-2">
                                            <div className={`p-3 rounded text-base font-medium h-12 flex items-center ${!actualPartData?.jobOrder ? 'bg-gray-800' : compareSapActual.jobOrder ? 'bg-indigo-950' : 'bg-yellow-900'}`}>
                                                {actualPartData?.jobOrder || '...'}
                                                {actualPartData?.jobOrder && !compareSapActual.jobOrder && sapData?.jobOrder && (
                                                    <span className="ml-2 text-yellow-400 text-xs">(Mismatch)</span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Part No Row */}
                                <div>
                                    <div className="flex">
                                        <div className="w-1/3 pr-2"><div className="mb-1 text-sm font-medium text-gray-400">PART NO</div></div>
                                        <div className="w-auto px-4"></div> {/* Spacer for icon */}
                                        <div className="w-1/3 px-2"><div className="mb-1 text-sm font-medium text-gray-400">PART NO</div></div>
                                        <div className="w-auto px-4"></div> {/* Spacer for icon */}
                                        <div className="w-1/3 pl-2"><div className="mb-1 text-sm font-medium text-gray-400">PART NO</div></div>
                                    </div>
                                    <div className="flex items-center">
                                        <div className="w-1/3 pr-2">
                                            <div className={`p-3 rounded text-base font-medium h-12 flex items-center ${!jobCardData?.partNumber ? 'bg-gray-800' : compareJobCardSap.partNo ? 'bg-indigo-950' : 'bg-yellow-900'}`}>
                                                {jobCardData?.partNumber ? jobCardData.partNumber.replace(/-/g, '') : '...'}
                                                {jobCardData?.partNumber && !compareJobCardSap.partNo && sapData?.partNumber && (
                                                    <span className="ml-2 text-yellow-400 text-xs">(Mismatch)</span>
                                                )}
                                            </div>
                                        </div>
                                        <div className="w-auto px-4 flex justify-center">
                                            {isRunning && (compareJobCardSap.partNo ? <CheckCircleIcon className="w-7 h-7 text-green-500" /> : <XCircleIcon className="w-7 h-7 text-red-500" />)}
                                        </div>
                                        <div className="w-1/3 px-2">
                                            <div className={`p-3 rounded text-base font-medium h-12 flex items-center ${sapData?.partNumber ? 'bg-indigo-950' : 'bg-gray-800'}`}>
                                                {sapData?.partNumber ? sapData.partNumber.replace(/-/g, '') : '...'}
                                            </div>
                                        </div>
                                        <div className="w-auto px-4 flex justify-center">
                                            {isRunning && (compareSapActual.partNo ? <CheckCircleIcon className="w-7 h-7 text-green-500" /> : <XCircleIcon className="w-7 h-7 text-red-500" />)}
                                        </div>
                                        <div className="w-1/3 pl-2">
                                            <div className={`p-3 rounded text-base font-medium h-12 flex items-center ${!actualPartData?.partNumber ? 'bg-gray-800' : compareSapActual.partNo ? 'bg-indigo-950' : 'bg-yellow-900'}`}>
                                                {actualPartData?.partNumber ? actualPartData.partNumber.replace(/-/g, '') : '...'}
                                                {actualPartData?.partNumber && !compareSapActual.partNo && sapData?.partNumber && (
                                                    <span className="ml-2 text-yellow-400 text-xs">(Mismatch)</span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {/* Quality Status Section */}
                    <div className="w-11/12 flex flex-col" style={{ height: '25%' }}>
                        <div className="bg-indigo-900 py-2 text-center text-white font-bold rounded-t text-lg">
                            QUALITY STATUS
                        </div>
                        <div className="py-4 text-center text-white bg-indigo-950 bg-opacity-50 rounded-b flex items-center justify-center" style={{ height: '100%' }}>
                            {(() => {
                                if (!isRunning) return null;
                                if (!actualPartData) return <div className="text-gray-400">No data available</div>;
                                
                                switch (actualPartData.quality_status) {
                                    case '1':
                                        return (
                                            <div className="flex items-center justify-center">
                                                <CheckCircleIcon className="w-7 h-7 text-green-500 mr-2" />
                                                <span className="text-green-500 font-bold text-lg">PASS</span>
                                            </div>
                                        );
                                    case '2':
                                        return (
                                            <div className="flex items-center justify-center">
                                                <XCircleIcon className="w-7 h-7 text-red-500 mr-2" />
                                                <span className="text-red-500 font-bold text-lg">FAIL</span>
                                            </div>
                                        );
                                    default:
                                        return <div className="text-gray-400">Not available</div>;
                                }
                            })()}
                        </div>
                    </div>
                    
                    {/* Production Status Section */}
                    <div className="w-11/12" style={{ height: '25%' }}>
                        <div className="bg-indigo-900 py-2 text-center text-white font-bold rounded-t text-lg">
                            PRODUCTION STATUS
                        </div>
                        <div className="py-2 text-center text-white bg-indigo-950 bg-opacity-50 rounded-b flex items-center justify-center" style={{ height: 'calc(100% - 2.5rem)' }}>
                            {!isRunning ? (
                                <div className="flex items-center justify-center">
                                    <XCircleIcon className="w-7 h-7 text-red-500 mr-2" />
                                    <span className="text-red-500 font-bold text-lg">STOPPED</span>
                                </div>
                            ) : (
                                <span className="text-yellow-400 font-bold text-lg">{status}</span>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
