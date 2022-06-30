(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanDetailController', TagihanDetailController);

  TagihanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$state', '$compile'];
  /* @ngInject */
  function TagihanDetailController($q, dataservice, logger, stateParams, scope, state, compile) {
    var vm = this;
    vm.title = 'Detail Tagihan';
    vm.table = 'tagihan';
    vm.data = {};
    vm.option = {};
    vm.verifiedTrans = [];
    vm.canCicil = true;

    vm.tambahPembiayaan = tambahPembiayaan;
    vm.hapusBukti = hapusBukti;
    vm.verif = verif;
    vm.tolak = tolak;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;

        vm.verifiedTrans = [];

        $.each(vm.data.transaksi, (i, v) => {
          if (v.status == 'verified') {
            vm.verifiedTrans.push(v);
          } else {
            if (v.status == 'process') {
              vm.canCicil = false;
            }
          }
        });
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function tambahPembiayaan() {
      let el = "<modal-bayar-tagihan data='vm.data'></modal-bayar-tagihan>";
      el = compile(el)(scope);
    }

    function hapusBukti(data) {
      dataservice.deleteRecord('tagihan_bukti_bayar', data.id).then(() => state.reload());
    }

    function verif(data) {
      dataservice.postData('transaksi', {'status': 'verified'}, data.id).then(() => state.reload());
    }

    function tolak(data) {
      dataservice.postData('transaksi', {'status': 'tolak'}, data.id).then(() => state.reload());
    }

  }
})();
