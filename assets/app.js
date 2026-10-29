document.getElementById('form-daftar')?.addEventListener('submit', function (e) {
  const nisn = this.querySelector('[name="nisn"]');
  const nik = this.querySelector('[name="nik"]');
  const hp = this.querySelector('[name="no_hp"]');
  const messages = [];
  if (nisn && !/^\d{10}$/.test(nisn.value.trim())) {
    messages.push('NISN harus 10 digit.');
  }
  if (nik && !/^\d{16}$/.test(nik.value.trim())) {
    messages.push('NIK harus 16 digit.');
  }
  if (hp && !/^(\+?62|0)8\d{8,12}$/.test(hp.value.replace(/\s+/g, ''))) {
    messages.push('Nomor WhatsApp tidak valid.');
  }
  if (messages.length) {
    e.preventDefault();
    alert(messages.join('\n'));
  }
});
