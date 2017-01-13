# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.network "forwarded_port", guest: 80, host: 7777
  config.vm.provider "virtualbox" do |vb|
    vb.name = "naf-dev"
    vb.memory = "1024"
  end
  config.vm.provision "shell", privileged: false, path: "provisioning/install-apache2-php7-commons.sh"
end
