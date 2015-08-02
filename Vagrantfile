# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.define "yarnyard-api-01" do |api|
    api.vm.box = "trusty"
    api.vm.host_name = "yarnyard-api-01"
    api.vm.network :private_network, ip: "192.168.56.103"
    api.vm.synced_folder "/home/mickadoo/workspace/yarnyard", "/var/www/yarnyard", id: "yarnyard-root",
      owner: "vagrant",
      group: "www-data",
      mount_options: ["dmode=775,fmode=664"]
    api.vm.provision :salt do |salt|
      salt.run_highstate = true
      salt.minion_config = "./app/config/vagrant/yarnyard_api_01.conf"
      salt.bootstrap_options = "-P"
    end
  end 
end
