(function() {
  'use strict';

  angular
    .module('app.tahun_ajaran')
    .controller('TahunAjaranFormController', TahunAjaranFormController);

  TahunAjaranFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope'];
  /* @ngInject */
  function TahunAjaranFormController($q, dataservice, logger, stateParams, scope) {
    var default_pendaftaran = {
      nama: "Biaya Pendaftaran",
      biaya_lunas: 0,
      total_biaya: 0,
      semester_id: null,
      lainnya: false,
      registrasi: true
    };

    var vm = this;
    vm.title = 'Form Tahun Ajaran';
    vm.table = 'tahun_ajaran';
    vm.semesters = [];
    vm.biaya_lainnyas = [];
    vm.data = {
      pembiayaan_tahun_ajar: [default_pendaftaran]
    };
    vm.bp_idx = 0;

    activate();

    scope.$watch('vm.data.paket_id', paketChanges);

    function activate() {
      var promises = [getSemester()];
      
      $q.all(promises).then(function() {
          promises = [getBiayaLainnya()];

          if (stateParams.dataId) {
            $q.all(promises).then(function() {
              getDataDetail();
            });
          }
      });

    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {

        let pembiayaan_tahun_ajar = vm.data.pembiayaan_tahun_ajar;

        if (response.data.pembiayaan_tahun_ajar.length > 0) {
          pembiayaan_tahun_ajar = response.data.pembiayaan_tahun_ajar;
        }

        vm.data = response.data;
        vm.data.pembiayaan_tahun_ajar = pembiayaan_tahun_ajar;
        vm.semesters = [];
        vm.biaya_lainnyas = [];
        let has_register = false;

        if (vm.data.pembiayaan_tahun_ajar.length > 0) {
          $.each(vm.data.pembiayaan_tahun_ajar, (i, v) => {
            if (v.lainnya) {
              vm.biaya_lainnyas.push(v);
            } else if (v.registrasi) {
              vm.bp_idx = i;
              has_register = true;
            }else {
              vm.semesters.push(v);
            }
          });
        }

        if (!has_register) {
          vm.data.pembiayaan_tahun_ajar.push(default_pendaftaran);
          vm.bp_idx = vm.data.pembiayaan_tahun_ajar.length - 1;
        }
      });
    }

    function getSemester() {
      return dataservice.getUrl('semester/get').then(function(response) {
        vm.semesters = response.data.filter((v) => v.tipe);

        if (vm.semesters.length > 0) {
          vm.data.pembiayaan_tahun_ajar = [];
          $.each(vm.semesters, (i, v) => {
            vm.data.pembiayaan_tahun_ajar.push({
              nama: "Biaya Semester " + v.nama,
              biaya_lunas: 0,
              total_biaya: 0,
              semester_id: v.id,
              lainnya: false,
              registrasi: false
            });
          });
        }
      });
    }

    function getBiayaLainnya() {
      return dataservice.getUrl('pembiayaan_lainnya/get').then(function(response) {
        vm.biaya_lainnyas = response.data;

        if (vm.biaya_lainnyas.length > 0) {
          $.each(vm.biaya_lainnyas, (i, v) => {
            vm.data.pembiayaan_tahun_ajar.push({
              nama: v.nama,
              biaya_lunas: v.biaya_lunas,
              total_biaya: v.total_biaya,
              semester_id: null,
              lainnya: true,
              registrasi: false
            });
          });
          vm.data.pembiayaan_tahun_ajar.push(default_pendaftaran);
          vm.bp_idx = vm.data.pembiayaan_tahun_ajar.length - 1;
        }

      });
    }

    function paketChanges(newVal, oldVal) {
      if (newVal && scope.selectedData) {
        let selected_data = scope.selectedData['vm.data.paket_id'];
        vm.data.paket = selected_data;
      }
    }
  }
})();
