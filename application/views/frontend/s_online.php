<section class="page-section">
    <div class="container">
        <?php if ($this->session->flashdata('success') == TRUE) : ?>
            <?= $this->session->flashdata('success'); ?>
        <?php endif; ?>
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Pengajuan Surat Online</h2>
            <h3 class="section-subheading text-muted">Isi Form Pengajuan Surat Dibawah:</h3>
        </div>
        <div class="text-justify pl-5 pr-5">
            <?= form_open_multipart('suratonline/ajukan', 'id="ajukanSurat"') ?>
            <div class="row">
                <div class="col-lg-6">
                    <label for="nik">NIK *</label>
                    <?= form_input(['name' => 'nik', 'type'=>'number', 'id' => 'nik', 'class' => 'form-control', "required" => "required", 'placeholder' => 'Silahkan masukkan NIK anda','maxlength'=>'16']); ?>
                </div>
                <div class="col-lg-6">
                    <label for="nama">Nama *</label>
                    <?= form_input(['name' => 'nama', 'id' => 'nama', 'class' => 'form-control inputs', "required" => "required", 'placeholder' => 'Silahkan masukkan nama anda']); ?>
                </div>
                <div class="col-lg-6 mt-2">
                    <label for="no_hp">No Hp *</label>
                    <?= form_input(['type' => 'text', 'name' => 'no_hp', 'id' => 'no_hp', 'class' => 'form-control inputs', "required" => "required", 'placeholder' => 'Silahkan masukkan No Hp anda']); ?>
                </div>
                <div class="col-lg-6 mt-2">
                    <label for="jenis">Pilih Jenis Surat *</label>
                    <?= form_dropdown('jenis_surat', $options, '', ['id' => 'jenis', 'class' => 'form-control inputs']); ?>
                </div>
                <div class="col-lg-12 file-div">
                    <div class="col-lg-12 mt-2">
                        <label for="file">File Berkas/Lampiran <sup class="text-danger">*PDF Recommended! | Max 5MB</sup></label>
                        <?= form_upload(['name' => 'files[]', 'id' => 'file', 'class' => 'form-control input-file inputs', 'multiple'=>'']) ?>
                    </div>
                </div>
            </div>
            <hr>
            <small>
                <p class="text-danger">PENTING!! Syarat Harus Terpenuhi, Jika Tidak Pengajuan Tidak Diproses!</p>
                <div id="syarat" class="text-danger">
                </div>
            </small>
            <hr>
            <div class="row mt-2">
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-block btn-primary inputs">KIRIM PERMOHONAN</button>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</section>
