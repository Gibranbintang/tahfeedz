<?php

    $koneksi = mysqli_connect("localhost:3306","root","","webti");

    if(!$koneksi)
    {
        die("koneksi Gagal!".mysqli_connect_error($koneksi));
    }

    function ambildata($query)
    {
        global $koneksi;

        $result = mysqli_query($koneksi,$query);

        $rows = []; /// array -> numeric
        while($row = mysqli_fetch_assoc($result))
        {
            $rows[] = $row;
        }

        return $rows; /// array


    }

    function tambahdatamhs($data)
    {
        global $koneksi;

        $nama = $data["nama"];
        $nim = $data["nim"];
        $prodi = $data["prodi"];
        $nohp = $data["nohp"];

        $file = $_FILES["foto"]["name"];
        $namefile = date('dmy_hms') . '_' . $file;
        $tmp = $_FILES["foto"]["tmp_name"];
        $folder = 'foto/';
        $path = $folder . $namefile;

        if(move_uploaded_file($tmp, $path))
        {

        $query = "INSERT INTO mahasiswa VALUES('', '$namafile' , '$nama', '$nim', '$prodi', '$nohp')";

        }
        mysqli_query($koneksi, $query);

        return mysqli_affected_rows($koneksi);
    }

    function hapusmhs($id)
    {
        global $koneksi;

        $query = "DELETE FROM mahasiswa WHERE id=$id";
        mysqli_query($koneksi, $query);

        return mysqli_affected_rows($koneksi);
    }

    function editdatamhs($data, $id)
    {
        global $koneksi;

        $nama = $data["nama"];
        $nim = $data["nim"];
        $prodi = $data["prodi"];
        $nohp = $data["nohp"];

        $query = "UPDATE mahasiswa set
        nama='$nama',
        nim='$nim',
        prodi='$prodi',
        nohp='$nohp'

        WHERE id=$id
        ";
        mysqli_query($koneksi, $query);

        return mysqli_affected_rows($koneksi);
    }

?>