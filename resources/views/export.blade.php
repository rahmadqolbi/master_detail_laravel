<?php 
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();//membuka halaman baru excel
$activeWorksheet = $spreadsheet->getActiveSheet();//menambahkan data ke worksheet 
//nama tabel header
$no_invoice_highlighted = $no_invoice;
$no_invoice = str_replace('<span class="highlight">', '', $no_invoice);//untuk menghapus value span
$no_invoice = str_replace('</span>', '', $no_invoice);

$activeWorksheet->setCellValue('A1', 'No Invoice');
$activeWorksheet->setCellValue('A2', 'Tanggal Pembelian');
$activeWorksheet->setCellValue('A3', 'Nama Pelanggan');
$activeWorksheet->setCellValue('A4', 'Jenis Kelamin');
$activeWorksheet->setCellValue('A5', 'Saldo');

//mengatur ukuran kolom
$activeWorksheet->getColumnDimension('A')->setWidth(20);
$activeWorksheet->getColumnDimension('C')->setWidth(30);
$activeWorksheet->getColumnDimension('D')->setWidth(30);
//nama tabel header kolom B
$activeWorksheet->setCellValue('B1', ':');
$activeWorksheet->setCellValue('B2', ':');
$activeWorksheet->setCellValue('B3', ':');
$activeWorksheet->setCellValue('B4', ':');
$activeWorksheet->setCellValue('B5', ':');

//nama tabel detail di kolom baris 7
$activeWorksheet->setCellValue('A7', 'NAMA BARANG');
$activeWorksheet->setCellValue('B7', 'QTY');
$activeWorksheet->setCellValue('C7', 'HARGA');
$activeWorksheet->setCellValue('D7', 'TOTAL HARGA');


$writer = new Xlsx($spreadsheet);
ob_start();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Export Data.xlsx"');
$writer->save('php://output');
ob_end_flush();


?>