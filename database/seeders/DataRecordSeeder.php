<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DataRecordSeeder extends Seeder
{
    public function run()
    {
        $dataSets = [
            // First group of 4 records
            [
                'barcodes_1' => 'BC0001', 'barcodes_2' => 'BC0002', 'barcodes_3' => 'BC0003', 'barcodes_4' => 'BC0004', 'barcodes_5' => 'BC0005', 'barcodes_6' => 'BC0006',
                'fg_no' => 'FG001', 'module_no' => 'MD001', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T001', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null, // This will be calculated
            ],
            [
                'barcodes_1' => 'BC0011', 'barcodes_2' => 'BC0012', 'barcodes_3' => 'BC0013', 'barcodes_4' => 'BC0014', 'barcodes_5' => 'BC0015', 'barcodes_6' => 'BC0016',
                'fg_no' => null, 'module_no' => null, 'flatness_status' => 'NG',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0011', 'barcodes_2' => 'BC0012', 'barcodes_3' => 'BC0013', 'barcodes_4' => 'BC0014', 'barcodes_5' => 'BC0015', 'barcodes_6' => 'BC0016',
                'fg_no' => 'FG001', 'module_no' => 'MD001', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T001', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0021', 'barcodes_2' => 'BC0022', 'barcodes_3' => 'BC0023', 'barcodes_4' => 'BC0024', 'barcodes_5' => 'BC0025', 'barcodes_6' => 'BC0026',
                'fg_no' => 'FG001', 'module_no' => 'MD001', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T001', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0031', 'barcodes_2' => 'BC0032', 'barcodes_3' => 'BC0033', 'barcodes_4' => 'BC0034', 'barcodes_5' => 'BC0035', 'barcodes_6' => 'BC0036',
                'fg_no' => 'FG001', 'module_no' => 'MD001', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T001', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0041', 'barcodes_2' => 'BC0042', 'barcodes_3' => 'BC0043', 'barcodes_4' => 'BC0044', 'barcodes_5' => 'BC0045', 'barcodes_6' => 'BC0046',
                'fg_no' => 'FG002', 'module_no' => 'MD002', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T002', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0051', 'barcodes_2' => 'BC0052', 'barcodes_3' => 'BC0053', 'barcodes_4' => 'BC0054', 'barcodes_5' => 'BC0055', 'barcodes_6' => 'BC0056',
                'fg_no' => 'FG002', 'module_no' => 'MD002', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T002', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0061', 'barcodes_2' => 'BC0062', 'barcodes_3' => 'BC0063', 'barcodes_4' => 'BC0064', 'barcodes_5' => 'BC0065', 'barcodes_6' => 'BC0066',
                'fg_no' => null, 'module_no' => null, 'flatness_status' => 'NG',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0061', 'barcodes_2' => 'BC0062', 'barcodes_3' => 'BC0063', 'barcodes_4' => 'BC0064', 'barcodes_5' => 'BC0065', 'barcodes_6' => 'BC0066',
                'fg_no' => 'FG002', 'module_no' => 'MD002', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T002', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0071', 'barcodes_2' => 'BC0072', 'barcodes_3' => 'BC0073', 'barcodes_4' => 'BC0074', 'barcodes_5' => 'BC0075', 'barcodes_6' => 'BC0076',
                'fg_no' => 'FG002', 'module_no' => 'MD002', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T002', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0081', 'barcodes_2' => 'BC0082', 'barcodes_3' => 'BC0083', 'barcodes_4' => 'BC0084', 'barcodes_5' => 'BC0085', 'barcodes_6' => 'BC0086',
                'fg_no' => null, 'module_no' => null, 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0091', 'barcodes_2' => 'BC0092', 'barcodes_3' => 'BC0093', 'barcodes_4' => 'BC0094', 'barcodes_5' => 'BC0095', 'barcodes_6' => 'BC0096',
                'fg_no' => null, 'module_no' => null, 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0101', 'barcodes_2' => 'BC0102', 'barcodes_3' => 'BC0103', 'barcodes_4' => 'BC0104', 'barcodes_5' => 'BC0105', 'barcodes_6' => 'BC0106',
                'fg_no' => null, 'module_no' => null, 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0111', 'barcodes_2' => 'BC0112', 'barcodes_3' => 'BC0113', 'barcodes_4' => 'BC0114', 'barcodes_5' => 'BC0115', 'barcodes_6' => 'BC0116',
                'fg_no' => null, 'module_no' => null, 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => null, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0121', 'barcodes_2' => 'BC0122', 'barcodes_3' => 'BC0123', 'barcodes_4' => 'BC0124', 'barcodes_5' => 'BC0125', 'barcodes_6' => 'BC0126',
                'fg_no' => null, 'module_no' => 'MD003', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T003', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0131', 'barcodes_2' => 'BC0132', 'barcodes_3' => 'BC0133', 'barcodes_4' => 'BC0134', 'barcodes_5' => 'BC0135', 'barcodes_6' => 'BC0136',
                'fg_no' => null, 'module_no' => 'MD003', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T003', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0141', 'barcodes_2' => 'BC0142', 'barcodes_3' => 'BC0143', 'barcodes_4' => 'BC0144', 'barcodes_5' => 'BC0145', 'barcodes_6' => 'BC0146',
                'fg_no' => null, 'module_no' => 'MD003', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T003', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            [
                'barcodes_1' => 'BC0151', 'barcodes_2' => 'BC0152', 'barcodes_3' => 'BC0153', 'barcodes_4' => 'BC0154', 'barcodes_5' => 'BC0155', 'barcodes_6' => 'BC0156',
                'fg_no' => null, 'module_no' => 'MD003', 'flatness_status' => 'OK',
                'group1_1' => 0.06, 'group1_2' => 0.06, 'group1_3' => 0.06, 'group1_4' => 0.06,
                'group2_1' => 0.06, 'group2_2' => 0.06, 'group2_3' => 0.06, 'group2_4' => 0.06,
                'group3_1' => 0.06, 'group3_2' => 0.06, 'group3_3' => 0.06, 'group3_4' => 0.06,
                'group4_1' => 0.06, 'group4_2' => 0.06, 'group4_3' => 0.06, 'group4_4' => 0.06,
                'resistance_1' => 252, 'resistance_2' => 252, 'resistance_3' => 252, 'resistance_4' => 252, 'resistance_5' => 252, 'resistance_6' => 252,
                'voltage_1' => 3.24, 'voltage_2' => 3.24, 'voltage_3' => 3.24, 'voltage_4' => 3.24, 'voltage_5' => 3.24, 'voltage_6' => 3.24,
                'temp' => 28, 'tub_no' => 'T003', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'parent_id' => null,
            ],
            // Add more records as needed following the same structure...
        ];

        $parentBarcode = null; // Tracks the barcode to assign as parent
        $okCount = 0;           // Counts 'OK' records within each group of 4

        foreach ($dataSets as $key => $data) {
            if ($data['flatness_status'] === 'OK') {
                if ($okCount % 4 === 0) {
                    // For each new group, set the parent barcode
                    $parentBarcode = $data['barcodes_1'];
                }
                
                // Set parent_id for the current record
                $data['parent_id'] = $parentBarcode;
                $okCount++; // Increase count only for 'OK' records
            } else {
                // If status is NG, exclude from the group by setting parent_id to null
                $data['parent_id'] = null;
            }

            // Insert the record into the database
            DB::table('data_records')->insert($data);
        }
    }
}
