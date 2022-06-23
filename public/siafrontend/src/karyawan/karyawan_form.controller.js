(function() {
  'use strict';

  angular
    .module('app.karyawan')
    .controller('KaryawanFormController', KaryawanFormController);

  KaryawanFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope'];
  /* @ngInject */
  function KaryawanFormController($q, dataservice, logger, stateParams, scope) {
    var vm = this;
    vm.title = 'Form Karyawan';
    vm.table = 'karyawan';
    vm.role_ids = {};
    vm.data = {
      orang: {
        user: {
          role: []
        }
      }
    };

    activate();


    scope.$watch(() => vm.role_ids, (newVal, oldVal) => {
      if (newVal) {
        vm.data.orang.user.role = [];
        $.each(newVal, (i ,v) => {
          if (v) {
            vm.data.orang.user.role.push({id: v});
          }
        });
      }
    }, true);

    function activate() {
      var promises = [getOption()];
      if (stateParams.dataId) {
        promises.push(getDataDetail());
      }
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;

        if (vm.data.orang.user.role) {
          $.each(vm.data.orang.user.role, (i ,v) => {
            if (v) {
              vm.role_ids[v.id] = v.id;
            }
          });
        }
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }
  }
})();
