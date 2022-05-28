(function() {
  'use strict';

  angular
    .module('app.tahun_ajaran')
    .controller('TahunAjaranDetailController', TahunAjaranDetailController);

  TahunAjaranDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope', '$element'];
  /* @ngInject */
  function TahunAjaranDetailController($q, dataservice, logger, stateParams, compile, scope, element) {
    var vm = this;
    vm.title = 'Detail Tahun Ajaran';
    vm.table = 'tahun_ajaran';
    vm.data = {};
    vm.data.biaya_semester = [];
    vm.data.biaya_lainnya = [];
    vm.data.biaya_pendaftaran = {};

    vm.buatTagihan = buatTagihan;
    vm.pilihMahasiswa = pilihMahasiswa;
    vm.gantiSemester = gantiSemester;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;

        if (vm.data) {
          vm.data.biaya_semester = [];
          vm.data.biaya_lainnya = [];
          vm.data.biaya_pendaftaran = {};

          if (vm.data.pembiayaan_tahun_ajar) {
            $.each(vm.data.pembiayaan_tahun_ajar, (i, v) => {
              if (v.lainnya) {
                vm.data.biaya_lainnya.push(v);
              } else if (v.registrasi) {
                vm.data.biaya_pendaftaran = v;
              } else {
                vm.data.biaya_semester.push(v);
              }
            });
          }
        }

      });
    }

    function buatTagihan(data) {
      vm.active_data = data;
      let el = "<modal-tagihan-ta data='vm.active_data'></modal-tagihan-ta>";
      el = compile(el)(scope);
    }

    function pilihMahasiswa(data) {
      vm.active_data = data;
      let el = "<modal-pilih-mahasiswa data='vm.active_data'></modal-pilih-mahasiswa>";
      el = compile(el)(scope);
      $(element).append(el);
    }

    function gantiSemester() {
      let el = "<modal-ganti-semester data='vm.data'></modal-ganti-semester>";
      el = compile(el)(scope);
      $(element).append(el);
    }
  }
})();
