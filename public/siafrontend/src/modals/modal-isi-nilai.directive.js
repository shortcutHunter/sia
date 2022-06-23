(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalIsiNilai', modalIsiNilai);

  modalIsiNilai.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalIsiNilai(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/isi_nilai',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.form_data = {};
      scope.autofill_data = {};
      scope.auto = {};
      scope.isiNilai = isiNilai;
      scope.isiNilaiSubmit = isiNilaiSubmit;

      scope.$watch('form_data', formDataChanges, true);
      scope.$watch('autofill_data', autoFillDataChanges, true);

      activate();

      function activate() {
        $(element).modal('show');

        getMahasiswa();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function getMahasiswa() {
        dataservice.getUrl(`/matkul_diampuh/${scope.data.id}/get/mahasiswa`).then(function(response){
          scope.data_mahasiswa = response.data;
          $.each(scope.data_mahasiswa, function(i, v){
            if (v.nilai) {
              $.each(v.nilai.riwayat_belajar_nilai, function(idx, val){
                if (val.nilai) {
                  scope.form_data[v.id + '_' + val.nilai_id] = val.nilai;
                }
              });

              scope.auto['absolut_' + v.id] = v.nilai.nilai_bobot;
              scope.autofill_data['akumulasi_' + v.id] = v.nilai.nilai_absolut;
            }
          });

        });
      }

      function getBobot(nilai)
      {
        let mutu = "E";
        if (nilai >= 79) {
            mutu = "A";
        }
        else if (nilai >= 68) {
            mutu = "B";
        }
        else if (nilai >= 60) {
            mutu = "C";
        }
        else if (nilai >= 41) {
            mutu = "D";
        }else{
            mutu = "E";
        }
        return mutu;
      }

      function formDataChanges(newVal, oldVal) {
        if (newVal != oldVal) {
          $.each(scope.data_mahasiswa, (i, v) => {
            let akumulasi = 0;
            let nilaiTerisi = false;

            $.each(scope.data.konfigurasi_nilai, (idx, val) => {
              let nilai_value = newVal[v.id + '_' + val.nilai.id];
              if (nilai_value) {
                akumulasi += (val.persentase * nilai_value / 100);
              }
              if (nilai_value != undefined) {
                nilaiTerisi = true;
              }
            });

            if (akumulasi > 0) {
              scope.autofill_data['terisi_' + v.id] = true;
              scope.autofill_data['akumulasi_' + v.id] = akumulasi;
            } else {
              if (nilaiTerisi) {
                scope.autofill_data['terisi_' + v.id] = false;
                scope.autofill_data['akumulasi_' + v.id] = akumulasi;
              }
            }

          });
        }
      }

      function autoFillDataChanges(newVal, oldVal) {
        if (newVal != oldVal) {
          $.each(scope.data_mahasiswa, (i, v) => {
            let akumulasi = newVal['akumulasi_' + v.id];

            scope.auto['absolut_' + v.id] = getBobot(akumulasi);
          });
        }
      }

      function getDataNilai()
      {
        let data = [];
        $.each(scope.data_mahasiswa, (i, v) => {
          let akumulasi = scope.autofill_data['akumulasi_' + v.id];

          if (akumulasi) {
            let nilai_data = [];
            $.each(scope.data.konfigurasi_nilai, (idx, val) => {
              let nilai_value = scope.form_data[v.id + '_' + val.nilai.id];
              nilai_data.push({
                nilai_id: val.nilai.id,
                nilai: nilai_value
              });
            });

            let riwayat_belajar = {
              mahasiswa_id: v.id,
              semester_id: v.semester_id,
              riwayat_belajar_detail: {
                mata_kuliah_id: scope.data.mata_kuliah_id,
                riwayat_belajar_nilai: nilai_data,
                nilai_bobot: scope.auto['absolut_' + v.id],
                nilai_absolut: akumulasi
              }
            };
            data.push(riwayat_belajar);
          }

        });

        return data;
      }

      function isiNilai() {
        let data = getDataNilai();
        let sendData = {'data': data};

        dataservice.postDataUrl('/proses/nilai/mahasiswa', sendData).then(function(response){
          $(element).modal('hide');
          state.reload();
        });
      }

      function isiNilaiSubmit() {
        let data = getDataNilai();
        let sendData = {
          'data': data,
          'submit': true,
          'matkul_diampuh_id': scope.data.id
        };

        dataservice.postDataUrl('/proses/nilai/mahasiswa', sendData).then(function(response){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();
