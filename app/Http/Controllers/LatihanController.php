<?php

namespace App\Http\Controllers;
use App\Models\LatihanModel;
use App\Models\LatihanModel2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;

use stdClass;
use Exception;

class LatihanController extends Controller
{
 //NOTE - Controller , merupakan bagian yang menghubungkan model dan view pada setiap proses request dari user sekaligus mengatur model dalam mengolah data.
public function index(Request $request) 
{
    
    $query = DB::table('penjualan');
    $query_result = DB::table('penjualan');
    //NOTE - untuk mengakses database penjualan
    $search = $request->input('global_search'); //NOTE - untuk menampung pengambilan data dari browser
    if (!empty($search)) {
        $query->where(function($q) use($search) {
            $q->where('no_invoice', 'LIKE', '%' . $search . '%')
                ->orWhere('no_invoice', 'LIKE', '%' . $search . '%')
                ->orWhere('tgl_pembelian', 'LIKE', '%' . $search . '%')
                ->orWhere('nama_pelanggan', 'LIKE', '%' . $search . '%')
                ->orWhere('jenis_kelamin', 'LIKE', '%' . $search . '%')
                ->orWhere('saldo', 'LIKE', '%' . $search . '%');
        });
        //NOTE - penggunaan query builder dalam Laravel untuk melakukan pencarian data pada tabel "penjualan" berdasarkan kata kunci tertentu yang disimpan dalam variabel $search.
        $query_result->where(function($q) use($search) {
            $q->where('no_invoice', 'LIKE', '%' . $search . '%')
                ->orWhere('no_invoice', 'LIKE', '%' . $search . '%')
                ->orWhere('tgl_pembelian', 'LIKE', '%' . $search . '%')
                ->orWhere('nama_pelanggan', 'LIKE', '%' . $search . '%')
                ->orWhere('jenis_kelamin', 'LIKE', '%' . $search . '%')
                ->orWhere('saldo', 'LIKE', '%' . $search . '%');
        });
    }
  
    if ($request->has('filters')) //Kondisi yang mengecek apakah request yang diterima dari browser mengandung parameter 'filters'. Jika parameter tersebut ada, maka blok kode yang berada di dalamnya akan dijalankan.
    {
        $filters = json_decode($request->filters, true); //NOTE - Mengubah nilai dari parameter 'filters' yang dikirimkan oleh pengguna menjadi array PHP
        if(!empty($filters['rules'])) 
        {
            $i = 1;
            foreach ($filters['rules'] as $rules) 
            {
                if ($i === 1) {
                    $query->where($rules['field'], 'like', '%'.$rules['data'].'%');
                    $query_result->where($rules['field'], 'like', '%'.$rules['data'].'%');
                } 
                else 
                {
                    $query->where($rules['field'], 'like', '%'.$rules['data'].'%');
                    $query_result->where($rules['field'], 'like', '%'.$rules['data'].'%');
                }

                $i++;
            }
        }
    }
    $count = $query_result->count();
    $limit = $request->input('rows', 10);
    $page = $request->input('page', 1);
    $sidx = $request->input('sidx', 'no_invoice');
    $sord = $request->input('sord', 'asc');
    
    $total_pages = ceil($count / $limit);
    //NOTE - Mencari total halaman yang dibutuhkan dengan membagi jumlah record dengan jumlah record per halaman yang diberikan, kemudian dibulatkan ke atas menggunakan fungsi ceil().
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    //NOTE - Jika halaman yang dipilih user lebih besar dari total halaman yang tersedia. Jika ya, maka halaman akan diatur ke halaman terakhir.
    $start = $limit * ($page - 1);
    //NOTE - $start = 10 * (3 - 1) = 20
    //NOTE - Menghitung indeks awal data yang harus ditampilkan pada halaman yang dipilih dengan mengurangi 1 dari nomor halaman yang dipilih, kemudian mengalikannya dengan jumlah record per halaman.
    $rows = $query->orderBy($sidx, $sord)->offset($start)->limit($limit)->get();
    //NOTE - Menjalankan query untuk mengambil data pada halaman yang dipilih dengan mengurutkan data menggunakan kolom yang dipilih, kemudian membatasi jumlah data yang diambil menggunakan fungsi offset() dan limit().
    // if ($rows->count() === 0 && $total_pages > 1 && $page === $total_pages) {
    //     $page--;
    //     $total_pages = ceil(($count - 1) / $limit);
    //     $start = $limit * ($page - 1);
    //     $rows = $query->orderBy($sidx, $sord)->offset($start)->limit($limit)->get();
    // }
    //NOTE -// jika halaman terakhir kosong setelah penghapusan baris terakhir,
        // kurangi nilai halaman dan hitung ulang indeks awal data
    $rows = $rows->map(function($row) {
        $row->tgl_pembelian = date('d-m-Y', strtotime($row->tgl_pembelian));
        //penyebab saldo 10 menjadi 100 harus ditambah str_replacenya di sini di tampilannya bukan controller
        return $row;
    });
 
    //NOTE - map untuk mengubah setiap elemen dalam $rows sesuai dengan suatu aturan atau operasi tertentu
    $response = new stdClass();
    $response->page = $page; //Mengisi nilai atribut page pada $response
    $response->total = $total_pages; //Mengisi nilai atribut total_pages pada $response
    $response->records = $count; //Mengisi nilai atribut count pada $response
    $response->rows = $rows; //Mengisi nilai atribut rows pada $response
   
    return response()->json($response);
    
}

    public function header(){
        return view('home');
    }
   
    public function detail($no_invoice){
        //untuk menampilkan data detail penjualan
        $latihan2 = LatihanModel2::select('*')->where('no_invoice', $no_invoice)->get();
  $json2 = $latihan2->toJson();
  return response($json2);
}


 


//      public function master()
// {
//     //untuk menampilkan data master penjualan
//     $latihan = LatihanModel::all();//ambil data dari database
//     foreach ($latihan as $data) {
//         $data->tgl_pembelian = date('d-m-Y', strtotime($data->tgl_pembelian));
//     }
//     //NOTE - melakukan looping setiap data yang diambil dari database dan mengubah format tanggal menjadi d-m-y
//     $json = $latihan->toJson();
//     return response($json, 200)->header('Content-Type', 'application/json');

// }


  
    
    public function tambah(){
        return view('tambah');
    }
    // public function simpan(Request $request)
    // {
    //     if(empty($request->no_invoice) || empty($request->tgl_pembelian) || empty($request->nama_pelanggan) || empty($request->jenis_kelamin) || empty($request->saldo) || empty($request->nama_barang) || empty($request->qty) || empty($request->harga)) {
    //         return response()->json(['status' => 'error', 'message' => 'Data Tidak Tersimpan']);
    //     }
    
    //     DB::beginTransaction();
    //     try {
    //         $latihan = new LatihanModel;
    //         $latihan2 = new LatihanModel2;
    //         $latihan->no_invoice = strtoupper($request->no_invoice);
    //         $latihan->tgl_pembelian = date('Y-m-d', strtotime($request->input('tgl_pembelian')));
    //         $latihan->nama_pelanggan = strtoupper($request->nama_pelanggan);
    //         $latihan->jenis_kelamin = strtoupper($request->jenis_kelamin);
    //         $latihan->saldo = str_replace('.', '', $request->saldo);
    
    //         if (!$latihan->save()) {
    //             throw new Exception('Data penjualan gagal ditambahkan.');
    //         }
    
    //         // memeriksa apakah ada data barang yang tidak diisi
    //         $isDataBarangEmpty = false;
    //         foreach($request->nama_barang as $barang) {
    //             if(empty($barang)) {
    //                 $isDataBarangEmpty = true;
    //                 break;
    //             }
    //         }
    //         if($isDataBarangEmpty) {
    //             throw new Exception('Data barang tidak lengkap.');
    //         } else {
    //             for($i=0;$i<count($request->nama_barang);$i++) {
                   
    //                 if (empty($request->nama_barang[$i]) || empty($request->qty[$i]) || empty($request->harga[$i])) {
    //                     return response()->json(['status' => 'error', 'message' => 'Data Tidak Tersimpan']);
    //                 }
    //                 $qtytik = str_replace('.', '', $request->qty[$i]);
    //                 $hargatik = str_replace('.', '', $request->harga[$i]);
    
    //                 $latihan2 = new LatihanModel2;
    //                 $latihan2->no_invoice = strtoupper($request->no_invoice);
    //                 $latihan2->nama_barang = strtoupper($request->nama_barang[$i]);
    //                 $latihan2->qty = $qtytik;
    //                 $latihan2->harga = $hargatik;
    
    //                 if (!$latihan2->save()) {
    //                     throw new Exception('Data penjualan detail gagal ditambahkan.');
    //                 }
    //             }
    //         }
    //         DB::commit();
    //         $response = ['message' => 'Success! Data Inserted', 'no_invoice' => strtoupper($request->no_invoice)];
    //         return response()->json($response);
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         $response = ['message' => "Failed! " . $e->getMessage()];
    //         return response()->json($response, 500);
    //     }
    // }
    public function simpan(Request $request)
    {
         //NOTE - tambahkan fungsi alert jika eksekusi user gagal
    if (empty($request->tgl_pembelian) || empty($request->nama_pelanggan) || empty($request->jenis_kelamin) || empty($request->saldo) || empty($request->nama_barang) || empty($request->qty) || empty($request->harga)) {
        return response()->json(['status' => 'error', 'message' => 'Data Tidak Tersimpan']);
    }
    DB::beginTransaction();//NOTE - untuk memulai data transaction
    try {
        
        $last_invoice_no = DB::table('penjualan')->max('no_invoice');
        //NOTE - untuk mengambil nilai terbesar dari kolom 'no_invoice' pada tabel 'penjualan' dengan menggunakan metode 'max'
        $new_invoice_no = ($last_invoice_no) ? intval(substr($last_invoice_no, 3)) + 1 : 1;
        //Fungsi substr() digunakan untuk memotong string menjadi potongan-potongan kecil, sedangkan intval() digunakan untuk mengkonversi nilai numerik dari suatu variabel.
        //NOTE - untuk mengambil angka dari string nomor invoice terakhir dan mengubahnya menjadi integer untuk dijadikan nilai running number pada invoice baru.
        $no_invoice = 'INV' . str_pad($new_invoice_no, 4, '0', STR_PAD_LEFT);
        //NOTE - kode ini menambahkan awalan "INV" ke nomor invoice baru yang sudah dibuat di langkah sebelumnya. Kemudian, menggunakan str_pad() untuk menambahkan angka nol di depan nomor invoice baru jika panjangnya kurang dari 4 digit.
        $request->merge(['no_invoice' => $no_invoice]);
        
        $latihan = new LatihanModel;
        $latihan2 = new LatihanModel2;
        $latihan->no_invoice = strtoupper($request->no_invoice);
        $latihan->tgl_pembelian = date('Y-m-d', strtotime($request->input('tgl_pembelian')));
        $latihan->nama_pelanggan = strtoupper($request->nama_pelanggan);
        $latihan->jenis_kelamin = strtoupper($request->jenis_kelamin);
        $latihan->saldo = str_replace('.', '', $request->saldo);

        if (!$latihan->save()) {
            throw new Exception('Data penjualan gagal ditambahkan.');
        }

        // memeriksa apakah ada data barang yang tidak diisi
        $isDataBarangEmpty = false;
        foreach ($request->nama_barang as $barang) {
            if (empty($barang)) {
                $isDataBarangEmpty = true;
                break;
            }
        }
        if ($isDataBarangEmpty) {
            throw new Exception('Data barang tidak lengkap.');
        } else {
            for ($i = 0; $i < count($request->nama_barang); $i++) {
                if (empty($request->nama_barang[$i]) || empty($request->qty[$i]) || empty($request->harga[$i])) {
                    return response()->json(['status' => 'error', 'message' => 'Data Tidak Tersimpan']);
                }
                $qtytik = str_replace('.', '', $request->qty[$i]);
                $hargatik = str_replace('.', '', $request->harga[$i]);

                $latihan2 = new LatihanModel2;
                $latihan2->no_invoice = strtoupper($request->no_invoice);
                $latihan2->nama_barang = strtoupper($request->nama_barang[$i]);
                $latihan2->qty = $qtytik;
                $latihan2->harga = $hargatik;

                if (!$latihan2->save()) {
                    throw new Exception('Data penjualan detail gagal ditambahkan.');
                }
            }
        }
            DB::commit();
             //NOTE - Pada blok kode tersebut, DB::commit() digunakan untuk menandai akhir dari transaksi yang sukses. Sebelum perintah ini, semua operasi dalam transaksi akan dieksekusi.
                  
            $response = ['message' => 'Success! Data Inserted', 'no_invoice' => strtoupper($request->no_invoice)];
            return response()->json($response);
        } catch (Exception $e) {
             //NOTE - blok catch, DB::rollback() digunakan untuk membatalkan transaksi yang sedang berjalan. Hal ini dilakukan apabila terdapat error pada saat eksekusi transaksi sehingga data tidak tercommit dan dibatalkan.
            DB::rollback();
            $response = ['message' => "Failed! " . $e->getMessage()];
            return response()->json($response, 500);
            //NOTE - Jika salah satu operasi dalam transaksi tersebut gagal, maka semua operasi dalam transaksi tersebut akan di-rollback atau dikembalikan ke kondisi semula.
        }
        
    }
    
    
    


    
    public function edit($no_invoice) {
  
        $latihan = LatihanModel::select('*')->where('no_invoice', $no_invoice)->get();
        $latihan2 = LatihanModel2::select('*')->where('no_invoice', $no_invoice)->get();
        
        return view('edit', ['latihan' => $latihan, 'latihan2' => $latihan2]);
    }



    public function simpan_edit(Request $request){
      
        DB::beginTransaction(); //NOTE - untuk memulai data transaction
        $no_invoice = $request->no_invoice;
        try {//NOTE - try untuk menangani kesalahan dalam menjalani transaction
            $latihan = LatihanModel::where('no_invoice', $no_invoice)->first();
            if ($latihan) {
                $latihan->tgl_pembelian = date('Y-m-d', strtotime($request->input('tgl_pembelian')));
                $latihan->nama_pelanggan = strtoupper($request->nama_pelanggan);
                $latihan->jenis_kelamin = strtoupper($request->jenis_kelamin);
                $latihan->saldo = str_replace('.', '', $request->saldo);
                $latihan->save();
                
            
            //    dd($latihan);
                // Menghapus data detail penjualan berdasarkan no_invoice
                LatihanModel2::where('no_invoice', $no_invoice)->delete();
    
                $total_detail = count($request->nama_barang);
                for ($i = 0; $i < $total_detail; $i++) {
                    if (empty($request->nama_barang[$i]) || empty($request->qty[$i]) || empty($request->harga[$i])) {
                        return response()->json(['status' => 'error', 'message' => 'Data Tidak Tersimpan']);
                    }
                    
                    $qtytik = str_replace('.', '', $request->qty[$i]);
                    $hargatik = str_replace('.', '', $request->harga[$i]);
    
                    $latihan2 = new LatihanModel2();
                    $latihan2->no_invoice = $no_invoice;
                    $latihan2->nama_barang = strtoupper($request->nama_barang[$i]);
                    $latihan2->qty = $qtytik;
                    $latihan2->harga = $hargatik;
                    $latihan2->save();
                }
                
                DB::commit();
            //NOTE - Pada blok kode tersebut, DB::commit() digunakan untuk menandai akhir dari transaksi yang sukses. Sebelum perintah ini, semua operasi dalam transaksi akan dieksekusi.
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data not found']);
            }
        } catch (\Exception $e) {
              //NOTE - blok catch, DB::rollback() digunakan untuk membatalkan transaksi yang sedang berjalan. Hal ini dilakukan apabila terdapat error pada saat eksekusi transaksi sehingga data tidak tercommit dan harus dibatalkan.
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        //NOTE - Jika salah satu operasi dalam transaksi tersebut gagal, maka semua operasi dalam transaksi tersebut akan di-rollback atau dikembalikan ke kondisi semula.

 

    }
    
    
 
    public function delete($no_invoice){
        $latihan = LatihanModel::select('*')->where('no_invoice', $no_invoice)->get();
        $latihan2 = LatihanModel2::select('*')->where('no_invoice', $no_invoice)->get();
        
        return view('delete', ['latihan' => $latihan, 'latihan2' => $latihan2]);
    }



    public function proses_delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $latihan2 = LatihanModel2::where('no_invoice', $request->no_invoice)->delete();
            $latihan = LatihanModel::where('no_invoice', $request->no_invoice)->delete();
           
            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
      
    }
    
    

    public function reports(Request $request){
        $start = $request->input('start');
        $limit = $request->input('limit');
        $page = $request->input('page');
        $sidx = $request->input('sidx'); 
        $sord = $request->input('sord'); 
        $global_search = $request->input('global_search');
      
        // Hitung nilai offset
        $offset = $start - 1;
        $limit = $limit - $start + 1;
      
        // Menentukan nilai awal untuk variabel $sql
        $sql = DB::table('penjualan');
      
        if ($global_search !== null && $global_search !== 'undefined') {
          $sql->where('no_invoice', 'LIKE', '%' . $global_search . '%')
              ->orWhere('tgl_pembelian', 'LIKE', '%' . $global_search . '%')
              ->orWhere('nama_pelanggan', 'LIKE', '%' . $global_search . '%')
              ->orWhere('jenis_kelamin', 'LIKE', '%' . $global_search . '%')
              ->orWhere('saldo', 'LIKE', '%' . $global_search . '%');
        }
      
        if ($request->has('filters')) {
          $filters = json_decode($request->input('filters'), true);
      
          if (!empty($filters['rules'])) {
            foreach ($filters['rules'] as $rule) {
              if (isset($rule['field']) && isset($rule['data'])) {
                $sql->where($rule['field'], 'LIKE', '%' . $rule['data'] . '%');
              }
            }
          }
        }
      
        if (!empty($sidx) && !empty($sord)) {
          $sql->orderBy($sidx, $sord);
        }
      
        $data = $sql->skip($offset)->take($limit)->get()->toArray();
        //NOTE - skip($offset) : Melewatkan sejumlah baris data pada tabel database yang ditentukan oleh nilai offset.

        //NOTE - take($limit) : Mengambil sejumlah baris data pada tabel database yang ditentukan oleh nilai limit.

        function objectToArray($object) {
            $result = [];
            foreach ($object as $key => $value) {
                if (is_object($value)) {
                    $result[$key] = objectToArray($value);
                } else {
                    // Ubah format 
                    if ($key == 'saldo') {
                        $result[$key] = number_format($value, 0, ',', '.');
                    }elseif($key == 'tgl_pembelian'){
                        $result[$key] = date('d-m-Y', strtotime($value));
                    }else {
                        $result[$key] = $value;
                    }
                }
            }
            return $result;
        }
        
        
        $json = [];
        foreach ($data as $detail) {
            $penjualan_detail = DB::table('penjualan_detail')
                ->where('no_invoice', '=', $detail->no_invoice)
                ->get();
              
            $aray = [];
            foreach ($penjualan_detail as $dataDetail) {
                $aray[] = array_merge(objectToArray($detail), (array) $dataDetail);
            }
              
            if (empty($aray)) {
                $aray[] = objectToArray($detail);
            }
              
            $json[] = $aray;
        }
              
        $dataTotal = json_encode(['Master Detail' => $json]);
        return view('reports')->with(['dataTotal' => $dataTotal]);
        
        
      }
        
      public function export(Request $request){
        $page = $request->input('page');
        $sidx = $request->input('sidx');
        $no_invoice_raw = $request->input('no_invoice');
        $global_search = $request->input('global_search');
     
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
    
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
$no_invoice = $no_invoice_raw;

// menghilangkan tag HTML 'span' pada $no_invoice
$no_invoice = str_replace('<span class="highlight">', '', $no_invoice);
$no_invoice = str_replace('</span>', '', $no_invoice);
$penjualan = DB::table('penjualan')->where('no_invoice', $no_invoice)->get();


        $i = 1;
        foreach($penjualan as $row){
            $tgl_pembelian = date('d-m-Y', strtotime($row->tgl_pembelian));
            $saldo = number_format($row->saldo, 0, '.', '.');
            $no_invoice = $row->no_invoice;
            $nama_pelanggan = $row->nama_pelanggan;
            $jenis_kelamin = $row->jenis_kelamin;
           
            $activeWorksheet->setCellValue('C1', $no_invoice);
            $activeWorksheet->getStyle('A' . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    
            $activeWorksheet->setCellValue('C2', $tgl_pembelian);
            $activeWorksheet->setCellValue('C3', $nama_pelanggan);
    
            $activeWorksheet->setCellValue('C4', $jenis_kelamin);
            $activeWorksheet->setCellValue('C5', $saldo);
    
            $activeWorksheet->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    
            $i++;
        }

        $penjualan_detail = DB::table('penjualan_detail')
        ->select('nama_barang', 'qty', 'harga')
        ->where('no_invoice', $no_invoice)
        ->get();

        $i = 8;
        foreach ($penjualan_detail as $baris) {
            $harga = $baris->harga;
            $harga = str_replace(".", "", $harga); // Menghilangkan tanda titik pada harga
            
            $activeWorksheet->setCellValue("A$i", $baris->nama_barang);
            $activeWorksheet->setCellValue("B$i", $baris->qty);
            $activeWorksheet->setCellValue("C$i", $harga);
            $activeWorksheet->setCellValue("D$i", "=B$i*C$i"); // Menggunakan formula untuk menghitung total harga
            
            $activeWorksheet->setCellValue("C".($i+1), 'Sub Total');
            $activeWorksheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('#,##0');
            $activeWorksheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('#,##0');
            $activeWorksheet->getStyle('C' . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $activeWorksheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('#,##0');
            
            $i++;
        }
        
        $activeWorksheet->getStyle('C'.($i))->getFont()->setBold(true);
        $activeWorksheet->getStyle('D'.($i))->getFont()->setBold(true);
        
        $activeWorksheet->setCellValue("C$i", 'Total Harga');
        $activeWorksheet->setCellValue("D$i", "=SUM(D8:D".($i-1).")"); // Menggunakan formula untuk menjumlahkan total harga
        $activeWorksheet->getStyle("D$i")->getNumberFormat()->setFormatCode('#,##0');
        $activeWorksheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('#,##0');
        
// Mengatur border pada kolom total harga
// $activeWorksheet->getStyle("D8:D".($i-1))->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
// $activeWorksheet->getStyle("D8:D".($i-1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$writer = new Xlsx($spreadsheet);
$filename = "Invoice " . $no_invoice . ".xlsx";

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit();
      }
   






      public function getPosition(Request $request, $no_invoice)
      {
          $no_invoice = $request->input('no_invoice');
          $sortfield = $request->input('sidx');
          $sortorder = $request->input('sord');
          $global_search = $request->input('global_search');
          $filters = $request->input('filters');
          
          // Menambahkan query filters
          $filter_query = "";
          if (!empty($filters)) {
              $filter_array = json_decode($filters, true);
              foreach ($filter_array['rules'] as $rule) {
                  $filter_query .= " AND " . $rule['field'] . " LIKE '%" . $rule['data'] . "%'";
              }
          }
      
          // Mengambil semua nomor invoice dari database
        //   $invoices = LatihanModel::orderBy($sortfield, $sortorder, $global_search, $filters)->get()->toArray();
      
          // Mencari posisi nomor invoice di array menggunakan array_search
        //   $position = array_search($no_invoice, array_column($invoices, 'no_invoice'));
          
        //   if ($position === false) {
        //     return response()->json(['no_invoice' => $no_invoice, 'position' => '1']);
        // }
          // Membuat query untuk mengambil posisi nomor invoice dari database
          $data = "SELECT temp.position
          FROM (
              SELECT ROW_NUMBER() OVER(ORDER BY $sortfield $sortorder) AS position, penjualan.* 
              FROM penjualan
              WHERE (penjualan.no_invoice LIKE '%$global_search%' OR penjualan.tgl_pembelian LIKE '%$global_search%' OR penjualan.nama_pelanggan LIKE '%$global_search%' OR penjualan.jenis_kelamin LIKE '%$global_search%' OR penjualan.saldo LIKE '%$global_search%')" . $filter_query . "
          ) temp
          WHERE temp.no_invoice = '$no_invoice'";
        
          // NOTE - fungsi ROW_NUMBER () digunakan untuk menghasilkan nilai posisi untuk setiap nomor
          // Menjalankan query dan mengambil hasilnya
          $result = DB::select($data);
       //NOTE - $result = DB::select($data) adalah fungsi yang menjalankan query SQL $data dan mengembalikan hasilnya dalam bentuk array.

          // Mengambil nilai posisi dari hasil query
          $pos = !empty($result[0]->position) ? intval($result[0]->position) : 0;
          //Kode $result[0] digunakan untuk mengambil hasil query pertama yang dihasilkan oleh DB::select($data)
          // Mengembalikan response dengan nomor invoice dan posisi
          return response()->json(['no_invoice' => $no_invoice, 'position' => $pos]);
      }



  



    
    
    
    
    
        
        
    
       
    
    
  
      
}



