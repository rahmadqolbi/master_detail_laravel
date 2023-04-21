// Mulai transaction
DB::beginTransaction();

try {
    // Query untuk menyimpan $latihan
    $latihan->save();

    // Query untuk menyimpan $latihan2
    for ($i = 0; $i < count($request->nama_barang); $i++) {
        $latihan2 = new LatihanModel2;
        $qtytik = str_replace('.', '', $request->qty[$i]);
        $hargatik = str_replace('.', '', $request->harga[$i]);

        $latihan2->no_invoice = strtoupper($request->no_invoice);
        $latihan2->nama_barang = strtoupper($request->nama_barang[$i]);
        $latihan2->qty = $qtytik;
        $latihan2->harga = $hargatik;
        $latihan2->save();
    }

    // Commit transaction jika tidak ada error
    DB::commit();

    // Response success
    $response = ['message' => 'Success! Data Inserted', 'no_invoice' => strtoupper($request->no_invoice)];
    return response()->json($response);
} catch (Exception $e) {
    // Rollback transaction jika terdapat error
    DB::rollback();

    // Response error
    return response()->json(['message' => 'Failed! Data not Inserted']);
}
