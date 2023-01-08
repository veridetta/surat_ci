<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suratonline extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('galery_model', 'galery');
        $this->load->model('pengajuan_track_model', 'pengajuan_track');
        $this->load->model('M_Penduduk', 'penduduk');

        $this->load->helper(array('form', 'url', 'Cookie', 'String'));
        $this->load->library('form_validation');
    }

    public function index()
    {
        // $data = $this->dashboard->user();
        $data['profil'] = $this->galery->profil();
        $judul = [
            'title' => 'Pengajuan Surat Online',
            'sub_title' => ''
        ];

        $data['options'] = [
            'Pilih',
            'Surat Pengantar:' => [
                'SPKK' => 'Kartu Keluarga',
                'SPNA' => 'Nikah(N.A)',
            ],
            'Surat Keterangan:' => [
                'SKKL' => 'Kelahiran',
                'SKKM' => 'Kematian',
                'SKP' => 'Pindah',
                'SKD' => 'Datang',
                'SKBM' => 'Belum Menikah',
                'SKPH' => 'Penghasilan',
                'SKM' => 'Miskin',
                'SKU' => 'Usaha',
                'SKT' => 'Tanah',
                'SKGG' => 'Ganti Rugi',
            ],
            'Rekomendasi Surat:' => [
                'SITU' => 'Izin Tempat Usaha',
                'SIMB' => 'Izin Mendirikan Bangunan',
            ],
        ];

        // $data['sm'] = $this->db->get('surat_masuk')->row_array();
        // var_dump($data);
        $this->load->view('frontend/header2', $judul);
        $this->load->view('frontend/s_online', $data);
        $this->load->view('frontend/footer2', $data);
    }

    public function ajukan()
    {
        $status = [
            1 => 1,  // Pending
            2 => 2,  // Diterima dan Dilanjutkan
            3 => 3,  // Sudah Diketik dan Diparaf
            4 => 4,  // Sudah Ditandatangani Lurah dan Selesai
        ];

        $nama = $this->input->post('nama', TRUE);
        $nik = $this->input->post('nik', TRUE);
        $no_hp = $this->input->post('no_hp', TRUE);
        $jenis_surat = $this->input->post('jenis_surat', TRUE);
        $this->load->library('form_validation');
        $this->load->model('pengajuan_track');

        $rules = $this->pengajuan_track->rules();
        $this->form_validation->set_rules($rules);
    
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('success', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h5><i class="icon fas fa-times"></i> Gagal!</h5> Nik Harus 16 Digit</div>');
            redirect(base_url("suratonline"));
        }
    
        $ceknik = $this->penduduk->cek_penduduk($nik)->num_rows();

        if ($ceknik <= 0) {
            $save = [
                'nik' => $nik,
                'nama' => $nama,
                'no_hp' => $no_hp,
            ];

            $this->db->insert('penduduk', $save);
            // $this->session->set_flashdata('success', '<div class="alert alert-warning alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h5><i class="icon fas fa-cross"></i> Maaf!</h5> NIK Anda tidak Terdaftar!</div>');
            // redirect(base_url("suratonline"));
        }

        //Output a v4 UUID 
        $rid = uniqid($jenis_surat, TRUE);
        $rid2 = str_replace('.', '', $rid);
        $rid3 = substr(str_shuffle($rid2), 0, 3);

        $cc = $this->db->count_all('pengajuan_surat') + 1;
        $count = str_pad($cc, 3, STR_PAD_LEFT);
        $id = $jenis_surat . "-";
        $d = date('d');
        $y = date('y');
        $mnth = date("m");
        $s = date('s');
        $randomize = $d + $y + $mnth + $s;
        $id = $id . $rid3 . $randomize . $count . $y;

        // var_dump($id);
        // die;
        $ceksurat = $this->pengajuan_track->cekDuplicate($nik,$jenis_surat)->num_rows();
        $data = array();
        if($ceksurat>0){
            $this->session->set_flashdata('success', '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h5><i class="icon fas fa-times"></i> Gagal!</h5> Anda sudah pernah mengajukan jenis surat yang sama.</div>');
            redirect(base_url("suratonline"));
        }else{
            $countfiles = count($_FILES['files']['name']);
      // Looping all files
      for($i=0;$i<$countfiles;$i++){
 
        if(!empty($_FILES['files']['name'][$i])){
 
          // Define new $_FILES array - $_FILES['file']
          $_FILES['file']['name'] = $_FILES['files']['name'][$i];
          $_FILES['file']['type'] = $_FILES['files']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['files']['error'][$i];
          $_FILES['file']['size'] = $_FILES['files']['size'][$i];
          $namafile = substr($_FILES['file']['name'][$i],-7);
          $file = $jenis_surat.uniqid().$namafile;
          // Set preference
          $config['upload_path'] = 'uploads/berkas'; 
          $config['allowed_types'] = '*';
          $config['max_size'] = '25000'; // max_size in kb
          $config['file_name'] = $file;
 
          //Load upload library
          $this->load->library('upload',$config); 
 
          // File upload
          if($this->upload->do_upload('file')){
            // Get data about the file
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];

            // Initialize array
            $data['filenames'][] = $filename;
          }
        }
      }
      $list_file=json_encode($data['filenames']);
        $datar = [
            'id' => $id,
            // 'nama' => $nama,
            'nik' => $nik,
            // 'no_hp' => $no_hp,
            'jenis_surat' => $jenis_surat,
            'file' => $list_file,
            'tanggal' => date('Y-m-d'),
            'status' => $status[1]
        ];

        // var_dump($data);
        // die;
        $this->pengajuan_track->insert_p_surat($datar);
        $this->session->set_flashdata('success', '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h5><i class="icon fas fa-check"></i> Selamat!</h5> Berhasil Mengajukan Surat! Berikut <b>ID</b> anda: <b>' . $id . '</b></div>');
        redirect(base_url("suratonline"));
        }
      // Count total files
      
    }
}
