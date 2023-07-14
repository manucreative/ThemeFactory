# Manu Creative Theme Factory TIMS (KRA) Magento 2 Module
## For this extension ensure you install the ZFP Lab Server On your server:

### Then run the following command: 
    /mnt/data/home/master/bin/zfplabserver
    ctrl + z
    bg
    disown -h
    You can verify as well by running:
    ps aux | grep "zfplabserver"

#### For more check this article: https://askubuntu.com/questions/8653/how-to-keep-processes-running-after-ending-ssh-session

## If you wish to run /home/master/bin/zfplabserver in background - just run it starting with nohup:
  nohup  /home/master/bin/zfplabserver.
## Use the following path to deploy the application
    app\code

# Build PWA-POS with Theme Factory's extensions

## For the changing of the receipt =>
  1. Change the template receipt and add the barcode you want:
      /Users/bqc/PhpstormProjects/alladin.co.ke/Source/client/pos/src/view/component/print/PrintComponent.js
  2. Then build POS and deploy, following below:

  - Please download newest release package (file attached)
  - All source code of PWA POS is in Source/client/pos
  - Please install npm to run build PWA Pos https://www.npmjs.com/get-npm
  - Upload your POS customize to source code of PWA POS
  - In folder Source/client/pos run command line “npm install”
  - To build POS please run “npm run build” in folder Source/client/pos
  - Please copy all data in folder Source/client/pos/build to folder Source/server/app/code/Magestore/- Webpos/build/apps/pos/ (If it doesn't have that folder please create it).
  - Run command line to install all extension of Magestore:
  - php bin/magento setup:upgrade;
  - php bin/magento setup:di:compile;
  - php bin/magento cache:flush;
  - Please run the command line “php bin/magento webpos:deploy” to deploy POS.

