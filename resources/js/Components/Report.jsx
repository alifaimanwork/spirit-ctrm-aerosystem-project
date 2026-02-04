import React from 'react';
import { Document, Page, Text, View, StyleSheet, PDFDownloadLink } from '@react-pdf/renderer';
import axios from 'axios';

// Create styles
const styles = StyleSheet.create({
    page: {
        padding: 30,
    },
    title: {
        fontSize: 24,
        marginBottom: 20,
    },
    table: {
        display: 'flex',
        width: 'auto',
        borderStyle: 'solid',
        borderWidth: 1,
        borderRightWidth: 0,
        borderBottomWidth: 0,
    },
    tableRow: {
        margin: 'auto',
        flexDirection: 'row',
    },
    tableCol: {
        width: '20%',
        borderStyle: 'solid',
        borderWidth: 1,
        borderLeftWidth: 0,
        borderTopWidth: 0,
    },
    tableCell: {
        margin: 'auto',
        marginTop: 5,
        marginBottom: 5,
        fontSize: 10,
    },
});

// Create Document Component
const PLCReport = ({ data }) => (
    <Document>
        <Page size="A4" style={styles.page}>
            <View style={styles.title}>
                <Text>PLC Report Data</Text>
            </View>
            <View style={styles.table}>
                {/* Table Header */}
                <View style={styles.tableRow}>
                    <View style={styles.tableCol}>
                        <Text style={styles.tableCell}>Report ID</Text>
                    </View>
                    <View style={styles.tableCol}>
                        <Text style={styles.tableCell}>Part Name</Text>
                    </View>
                    <View style={styles.tableCol}>
                        <Text style={styles.tableCell}>Part Number</Text>
                    </View>
                    <View style={styles.tableCol}>
                        <Text style={styles.tableCell}>Job Order</Text>
                    </View>
                    <View style={styles.tableCol}>
                        <Text style={styles.tableCell}>Quality Check</Text>
                    </View>
                </View>
                {/* Table Data */}
                {data.map((item, index) => (
                    <View style={styles.tableRow} key={index}>
                        <View style={styles.tableCol}>
                            <Text style={styles.tableCell}>{item.reportid}</Text>
                        </View>
                        <View style={styles.tableCol}>
                            <Text style={styles.tableCell}>{item.part_name}</Text>
                        </View>
                        <View style={styles.tableCol}>
                            <Text style={styles.tableCell}>{item.part_number}</Text>
                        </View>
                        <View style={styles.tableCol}>
                            <Text style={styles.tableCell}>{item.job_order}</Text>
                        </View>
                        <View style={styles.tableCol}>
                            <Text style={styles.tableCell}>
                                {item.quality_check === 1 ? 'OK' : 'NG'}
                            </Text>
                        </View>
                    </View>
                ))}
            </View>
        </Page>
    </Document>
);

const Report = ({ startDate, endDate, hideDownload = false }) => {
    const [reportData, setReportData] = React.useState([]);

    React.useEffect(() => {
        const fetchData = async () => {
            try {
                console.log('Fetching PLC report data with date range:', { startDate, endDate });
                const params = {};
                if (startDate) params.start_date = startDate;
                if (endDate) params.end_date = endDate;

                const response = await axios.get(route('hub.report.data'), { params });
                console.log('Received data:', response.data);
                
                // Sort data by reportid
                const sortedData = [...response.data].sort((a, b) => a.reportid - b.reportid);
                setReportData(sortedData);
            } catch (error) {
                console.error('Error fetching report data:', error);
            }
        };

        fetchData();
    }, [startDate, endDate]);

    React.useEffect(() => {
        console.log('Current reportData state:', reportData);
    }, [reportData]);

    if (hideDownload) {
        return null; // Don't render anything if hideDownload is true
    }

    return (
        <div className="mt-4">
            <PDFDownloadLink
                document={<PLCReport data={reportData} />}
                fileName="plc_report.pdf"
                className="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors duration-150 ease-in-out"
            >
                {({ blob, url, loading, error }) =>
                    loading ? 'Generating PDF...' : 'Download Report'
                }
            </PDFDownloadLink>
        </div>
    );
};

export default Report;
