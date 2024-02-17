<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute Harus Diterima.',
    'active_url'           => ':attribute Bukan Merupakan URL Yang Valid.',
    'after'                => ':attribute Harus Tanggal Setelah :date.',
    'after_or_equal'       => ':attribute Harus Tanggal Setelah Atau Sama Dengan :date.',
    'alpha'                => ':attribute Hanya Boleh Berisikan Huruf.',
    'alpha_dash'           => ':attribute Hanya Boleh Berisikan Huruf, Angka Dan Tanda Penghubung.',
    'alpha_num'            => ':attribute Hanya Boleh Berisikan Huruf Dan Angka.',
    'array'                => ':attribute Harus Berupa Array.',
    'before'               => ':attribute Harus Tanggal Sebelum :date.',
    'before_or_equal'      => ':attribute Harus Tanggal Sebelum Atau Sama Dengan :date.',
    'between'              => [
        'numeric' => ':attribute Harus Di Antara :min Dan :max.',
        'file'    => ':attribute Harus Di Antara :min Dan :max Kilobyte.',
        'string'  => ':attribute Harus Di Antara :min Dan :max Karakter.',
        'array'   => ':attribute Harus Di Antara :min Dan :max Item.',
    ],
    'boolean'              => 'Kolom :attribute Harus Benar Atau Salah.',
    'confirmed'            => 'Konfirmasi :attribute Tidak Sesuai.',
    'date'                 => ':attribute Bukan Merupakan Tanggal Yang Valid.',
    'date_format'          => ':attribute Tidak Sesuai Dengan Format :format.',
    'different'            => ':attribute Dan :other Harus Berbeda.',
    'digits'               => ':attribute Harus :digits Digit.',
    'digits_between'       => ':attribute Harus Di Antara :min Dan :max Digit.',
    'dimensions'           => ':attribute Memiliki Dimensi Gambar Yang Tidak Valid.',
    'distinct'             => 'Kolom :attribute Memiliki Nilai Duplikat.',
    'email'                => ':attribute Harus Merupakan Alamat Email Yang Valid.',
    'exists'               => ':attribute Ini Tidak Valid.',
    'file'                 => ':attribute Harus Berupa Berkas.',
    'filled'               => 'Kolom :attribute Harus Diisi.',
    'image'                => ':attribute Harus Berupa Gambar.',
    'in'                   => ':attribute Ini Tidak Valid.',
    'in_array'             => 'Kolom :attribute Tidak Ada Pada :other.',
    'integer'              => ':attribute Harus Berupa Integer.',
    'ip'                   => ':attribute Harus Merupakan Alamat IP Yang Valid.',
    'ipv4'                 => ':attribute Harus Merupakan Alamat IPv4 Yang Valid.',
    'ipv6'                 => ':attribute Harus Merupakan Alamat IPv6 Yang Valid.',
    'json'                 => ':attribute Harus Merupakan JSON String Yang Valid.',
    'max'                  => [
        'numeric' => ':attribute Harus Tidak Lebih Dari :max.',
        'file'    => ':attribute Harus Tidak Lebih Dari :max Kilobyte.',
        'string'  => ':attribute Harus Tidak Lebih Dari :max Karakter.',
        'array'   => ':attribute Tidak Memiliki Lebih Dari :max Item.',
    ],
    'mimes'                => ':attribute Harus Berupa Berkas : :values.',
    'mimetypes'            => ':attribute Harus Berupa Berkas : :values.',
    'min'                  => [
        'numeric' => ':attribute Setidaknya Harus :min.',
        'file'    => ':attribute Setidaknya Harus :min Kilobyte.',
        'string'  => ':attribute Setidaknya Harus :min Karakter.',
        'array'   => ':attribute Setidaknya Memiliki :min Item.',
    ],
    'not_in'               => ':attribute Ini Tidak Valid.',
    'numeric'              => ':attribute Harus Berupa Angka.',
    'present'              => 'Kolom :attribute Harus Ada.',
    'regex'                => 'Format :attribute Tidak Valid.',
    'required'             => 'Kolom :attribute Wajib Diisikan.',
    'required_if'          => 'Kolom :attribute Wajib Diisikan Jika Isian :other Adalah :value.',
    'required_unless'      => 'Kolom :attribute Wajib Diisikan Kecuali Jika Isian :other Adalah :values.',
    'required_with'        => 'Kolom :attribute Wajib Diisikan Jika Isian :values Ada.',
    'required_with_all'    => 'Kolom :attribute Wajib Diisikan Jika Isian :values Ada.',
    'required_without'     => 'Kolom :attribute Wajib Diisikan Jika Isian :values Tidak Ada.',
    'required_without_all' => 'Kolom :attribute Wajib Diisikan Jika Isian :values Tak Satu Pun Ada.',
    'same'                 => ':attribute Dan :other Harus Sesuai.',
    'size'                 => [
        'numeric' => ':attribute Harus :size.',
        'file'    => ':attribute Harus :size Kilobyte.',
        'string'  => ':attribute Harus :size Karakter.',
        'array'   => ':attribute Harus Berisikan :size Item.',
    ],
    'string'               => ':attribute Harus Berupa string.',
    'timezone'             => ':attribute Harus Merupakan Zona Yang Valid.',
    'unique'               => ':attribute Sudah Ada.',
    'uploaded'             => ':attribute Gagal Diunggah.',
    'url'                  => 'Format :attribute Tidak Valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'Pesan Kustom',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],
    'custom-messages' => [
        'quantity_not_available' => 'Hanya :qty :unit Tersedia',
        'this_field_is_required' => 'Kolom Ini Wajib Diisikan'
    ],

];