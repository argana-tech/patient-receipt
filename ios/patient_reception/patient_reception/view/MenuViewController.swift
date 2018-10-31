//
//  MenuViewController.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/26.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import SVProgressHUD
import MaterialComponents
import CoreLocation
import ChameleonFramework
import AudioToolbox
import CoreBluetooth
import UserNotifications

protocol MenuViewControllerDelegaete {
    func receipted(resultObject:ReceiptObject)
    func registerd(resultObject:ReceiptObject)
}
class MenuViewController: UIViewController, MenuViewControllerDelegaete, CLLocationManagerDelegate, CBCentralManagerDelegate, CBPeripheralDelegate {
    
    private static let  UM_50 = "UM-50"
    private static let MSG_NOTIFICATION_UNAUTHORIZED = "通知が許可されていません。アプリの通知を許可してください。"
    private static let MSG_LOCATION_UNAUTHORIZED = "位置情報が有効になっていません。設定画面で有効にしてください。"
    private static let MSG_BLUETOOTH_UNAUTHORIZED = "Bluetoothが有効になっていません。設定画面で有効にしてください。"
    
    @IBOutlet weak var qrDiscriptionLabel: UILabel!
    @IBOutlet weak var receptionDiscriptionLabbel: UILabel!
    @IBOutlet weak var receptionPossibleLabel: UILabel!
    
    @IBOutlet weak var registerButton: MDCRaisedButton!
    @IBOutlet weak var receptionButton: MDCRaisedButton!
    
    
    // BLE
    var centralManager: CBCentralManager!
    private var peripheralArray = [CBPeripheral]()
    private var serviceArray = [CBService]()
    private var characteristicArray = [CBCharacteristic]()
    
    var openViewController:UIViewController?
    
    private var beaconLastest:Date?;
    
    // 位置情報
    var geo:LocateObject.Geo?
    // ビーコン情報
    var beacons = [LocateObject.Beacon]()
    // 位置情報
    var locationManager: CLLocationManager!
    
    // Segue
    enum Segue:String {
        case registerView = "RegisterViewSegue"
        case receptionView = "ReceptionViewSegue"
    }
    
    // 設定取得
    private var settinsObject:SettingsObject?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        authorize()
        
        setUpNotification()
        
        // 画面表示の設定
        setupDisplay()

        // API設定取得
        settings()

        // BLE
        setupBLE()

        // GPS位置情報
        setupLocationManager()
    }
    
    
    private func alert(title: String, message:String, handler: ((UIAlertAction) -> Void)? = nil) {
        let alert = QUAlertController(title: title, message: message, preferredStyle: .alert)
        
//        alert.addAction("キャンセル", style: .Cancel) { (_) in
//            print("キャンセルが押された")
//        }
        
//        alert.addAction("おっけー", style: .default) { (_) in
//            handler(_)
//        }
        
        alert.addAction("OK", style: UIAlertActionStyle.default, handler: handler)
        
        alert.show()
//        let alertController = UIAlertController(title: title, message:message, preferredStyle: UIAlertControllerStyle.alert)
//        alertController.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: nil))
//
//
//        // すでにアラートが表示されている場合は、それを親 ViewController にする
//        var baseView: UIViewController = self
//        while baseView.presentedViewController != nil && !baseView.presentedViewController!.isBeingDismissed {
//            baseView = baseView.presentedViewController!
//
//        }
//
//        baseView.present(alertController, animated: true, completion: nil)
    }
    private func authorize() {
        
        UNUserNotificationCenter.current().getNotificationSettings { (settings) in
            switch settings.authorizationStatus {
            case .authorized:
                print("authorized")
                break
            default:
                self.alert(title: "通知設定", message:  MenuViewController.MSG_NOTIFICATION_UNAUTHORIZED)
                break
//            case .denied:
//                print("denied")
//
//                break
//            case .notDetermined:
//                print("notDetermined")
//                break
//            case .provisional:
//                print("notDetermined")
            }
            
            
        }
//        NotificationCenter.default.GET
//        NotificationCenter
////        NotificationCenter.default
//        NotificationCenter.current().getNotificationSettings { (settings) in
//
//            switch settings.authorizationStatus {
//            case .authorized:
//                break
//            case .denied:
//                break
//            case .notDetermined:
//                break
//            }
//        }
    
    }
    // BLEの設定
    private func setupBLE() {
        
        let options = [
            CBConnectPeripheralOptionNotifyOnConnectionKey: false,
            CBConnectPeripheralOptionNotifyOnDisconnectionKey: false,
            CBConnectPeripheralOptionNotifyOnNotificationKey: false
        ]
        self.centralManager = CBCentralManager(delegate: self, queue: nil, options: options)
    }
    
    // メッセージ受信
    private func setUpNotification () {
        // メッセージ受信レシーバー
        NotificationCenter.default.addObserver(
            self,
            selector: #selector(self.receivedMessage),
            name: Notification.Name.RecivedMessage,
            object: nil
        )
        
        // アプリ終了中に通知が来た場合の対応
        let appDelegate:AppDelegate = UIApplication.shared.delegate as! AppDelegate
        if let _userInfo = appDelegate.userInfo {
//            SVProgressHUD.showSuccess(withStatus: "通知来てた")
            NotificationCenter.default.post(name: Notification.Name.RecivedMessage, object: nil, userInfo: _userInfo as? [AnyHashable : Any])
        }
    }
    
    // メッセージ受信
    @objc private func receivedMessage(notification:Notification) {
        if let userInfo = notification.userInfo {
            
            if let _ = openViewController {
                openViewController?.dismiss(animated: false, completion: nil)
            }
            
            AudioServicesPlaySystemSound(SystemSoundID(kSystemSoundID_Vibrate))
            // トライトーン
            let soundID_1300: SystemSoundID = 1300
            AudioServicesPlaySystemSound(soundID_1300)
            
            //
            print(userInfo)
            let text = userInfo["text"] as! String
            let alertController = UIAlertController(title: "お呼出し", message: text, preferredStyle: UIAlertControllerStyle.alert)
            alertController.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: { (action) in
                print("OK")
                // 通知を削除
                UIApplication.shared.applicationIconBadgeNumber = 1
                UIApplication.shared.applicationIconBadgeNumber = 0
                
                self.read()
                
                
            }))
            self.present(alertController, animated: true) {
                print("閉じる")
                
            }
        } else {
            
        }
    }
    
    // 既読
    private func read() {
        let notificationObject = NotificationObject()
        notificationObject.uniqueId = UserData.getUniqueId()
        NotificationManager().postRead(notification: notificationObject) { (notificationResultObject) in
            if notificationResultObject.result {
                print("既読 OK")
            } else {
                print("既読 NG")
            }
        }
    }
    
    // 画面の設定
    private func setupDisplay() {
        
        // 受付メッセージ
        receptionDiscriptionLabbel.adjustsFontSizeToFitWidth = true;
        receptionDiscriptionLabbel.text = "";
        
        // 受付可能時間
        receptionPossibleLabel.adjustsFontSizeToFitWidth = true;
        receptionPossibleLabel.text = ""
        
        // QRコードメッセージ
        qrDiscriptionLabel.adjustsFontSizeToFitWidth = true;
        qrDiscriptionLabel.text = ""
        // ボタン活性
        self.registerButton.isEnabled = false
        self.receptionButton.isEnabled = false
        
        // 登録ボタン
        self.registerButton.setBackgroundColor(UIColor.flatOrangeDark)
        self.registerButton.setTitleColor(UIColor.white, for: UIControlState.normal)
        
        // 受付ボタン
        self.receptionButton.setBackgroundColor(UIColor.flatForestGreenDark)
        self.receptionButton.setTitleColor(UIColor.white, for: UIControlState.normal)
        
    }
    
    // 位置情報設定
    private func setupLocationManager() {
        locationManager = CLLocationManager()
        guard let locationManager = locationManager else { return }
        
        locationManager.delegate = self
        // アプリ使用中のみ
        locationManager.requestWhenInUseAuthorization()
        
        let status = CLLocationManager.authorizationStatus()
        if status == .authorizedWhenInUse {
            locationManager.activityType = .fitness
            locationManager.desiredAccuracy = kCLLocationAccuracyBest
            locationManager.distanceFilter = 0.5
            locationManager.startUpdatingLocation()
        } else {
            self.alert(title: "位置情報設定", message: MenuViewController.MSG_LOCATION_UNAUTHORIZED )
        }
    }
    
    func locationManager(_ manager: CLLocationManager, didStartMonitoringFor region: CLRegion) {
        print(region)
    }
    
    func locationManager(_ manager: CLLocationManager, didDetermineState state: CLRegionState, for region: CLRegion) {
        print(region)
    }
    
    // 位置情報取得失敗
    func locationManager(_ manager: CLLocationManager, didFailWithError error: Error) {
        print(error)
    }
    
    // 位置情報取得成功
    func locationManager(_ manager: CLLocationManager, didUpdateLocations locations: [CLLocation]) {
        
        let location = locations.first
        self.geo = LocateObject.Geo()
        self.geo!.latitude = location?.coordinate.latitude
        self.geo!.longitude = location?.coordinate.longitude

    }
    
    //  CentralManager状態の受信
    func centralManagerDidUpdateState(_ central: CBCentralManager) {
        
        switch central.state {
        case .poweredOff:
            print("poweredOff")
            alert(title: "Bluetooth設定", message: MenuViewController.MSG_BLUETOOTH_UNAUTHORIZED)
        case .poweredOn:
            print("poweredOn")
            let options:[String : Any] = [
                CBCentralManagerScanOptionAllowDuplicatesKey: true,
//                CBCentralManagerScanOptionSolicitedServiceUUIDsKey: uuidList
            ]
            central.scanForPeripherals(withServices: nil, options: options)

        case .resetting:
            print("resetting")
            alert(title: "Bluetooth設定", message: MenuViewController.MSG_BLUETOOTH_UNAUTHORIZED)
        case .unauthorized:
            print("unauthorized")
            alert(title: "Bluetooth設定", message: MenuViewController.MSG_BLUETOOTH_UNAUTHORIZED)
        case .unknown:
            print("unknown")
            alert(title: "Bluetooth設定", message: "Bluetoothが有効ではありません。設定でBluetoothを有効にしてください。")
        case .unsupported:
            print("unsupported")
            alert(title: "Bluetooth設定", message: "Bluetoothをサポートしていません")
        default:
            print("none")
        }
    }
    
    // Peripheral探索結果の受信(複数あれば複数回)
    func centralManager(_ central: CBCentralManager, didDiscover peripheral: CBPeripheral, advertisementData: [String : Any], rssi RSSI: NSNumber) {
        
//        if let name = peripheral.name {
        let uuid = peripheral.identifier.uuidString
        if let beaconUuidList = settinsObject?.beaconUuidList {
            if (beaconUuidList.contains(uuid)) {
                //            if  MenuViewController.UM_50 == name {
//                print("UUID: \(peripheral.identifier.uuidString)")
                if let _ = beaconLastest {
                    //
                } else {
                    beaconLastest = Date()
                }
                
                let now = Date()
                let diff = now.timeIntervalSince(beaconLastest!)
                if Double((settinsObject?.beaconCollectSpan)!) < (diff) {
                    let b = LocateObject.Beacon()
                    b.name = peripheral.identifier.uuidString
                    b.level = Int(truncating: RSSI)
                    b.time = DateUtil.date2String(date: Date(), format: .ymdHis)
                    beacons.append(b)
                    
                    beaconLastest = Date()
                }
                
            }
        }
        //
    }
    
    // 端末登録チェック
    private func checkRegisterdDevice() {
        let check = CheckObject()
        check.uniqueId = UserData.getUniqueId()
        CheckManager().postRegister(check: check) { (checkResult) in
            print("端末登録チェック")
            self.settingRegisterButton(checkObject: checkResult)
//                result: checkResult.result,
////                flg: checkResult.checkFlg,
//                flg: true,
//                message: checkResult.message,
//                button: checkResult.button )
            
        }
    }
    
    // 登録ボタンの状態変更
    private func settingRegisterButton(checkObject:CheckObject) {
//        private func settingRegisterButton(result:Bool, flg:Bool, message:String?, button:String?) {
        
        if checkObject.result {
            if checkObject.checkFlg {
                // 登録完了
                print("再登録")
                self.registerButton.setTitle(checkObject.button, for: UIControlState.normal)
                self.registerButton.isEnabled = checkObject.checkFlg
            } else {
                print("登録")
                self.registerButton.setTitle(checkObject.button, for: UIControlState.normal)
                self.registerButton.isEnabled = checkObject.checkFlg
            }
            
            self.qrDiscriptionLabel.text = checkObject.message
        } else {
            // 失敗なにもしない
        }
        
    }
    
    // 受付状況可否チェック
    private func checkReceiptPossible() {
        let check = CheckObject()
        check.uniqueId = UserData.getUniqueId()
        CheckManager().postTime(check: check) { (resultCheckObject) in
            self.receiptButtonEnable(resultCheck: resultCheckObject)
        }
    }
    
    
    func receiptButtonEnable (resultCheck:CheckObject) {
        print("受付状況可否チェック")
        if resultCheck.result {
            // OK
            if resultCheck.checkFlg {
                 self.receptionDiscriptionLabbel.text = resultCheck.message
                self.receptionButton.isEnabled = resultCheck.checkFlg
            } else {
                 self.receptionDiscriptionLabbel.text = resultCheck.message
                self.receptionButton.isEnabled = resultCheck.checkFlg
            }
//            // 受付
//            if ((self.settinsObject?.activeReceiptTime.isPossible())!) {
//                print("受付:OK")
//                self.receptionButton.isEnabled = true
//            } else {
//                print("受付:NG")
//                self.receptionButton.isEnabled = false
//            }
            
        } else {
            print("受付:NG")
            // NG
            self.receptionButton.isEnabled = false;
             self.receptionDiscriptionLabbel.text = resultCheck.message
            
        }
        // メッセージを設定
//        self.receptionDiscriptionLabbel.text = resultCheck.message
    }
    
    
    // 設定取得
    private func settings() {
        SVProgressHUD.show(withStatus: "情報取得中")
        let settingManager = SettingsManager()
        settingManager.getProerty { (settinsObject) in
            self.settinsObject = settinsObject
            SVProgressHUD.showSuccess(withStatus: "取得完了")
            // 送信可能時間
            if ((self.settinsObject?.activeSendTime.isPossible())!) {
                print("送信可能時間:OK")
                // Beaconタイマー
                self.beaconTimerStart()
                
                // 位置情報タイマー
                self.geoTimerStart()
                
                // 受付可否タイマー
                self.receiptPossibleTimer()
                
            } else {
                print("送信可能時間:NG")
            }
            
            
            // 端末登録チェック
            self.checkRegisterdDevice()
            
            // 受付状況可否チェック
            self.checkReceiptPossible()
            
            // 画面情報更新
            self.initDisplay()
        }
    }
    
    // 受付可否インターバル
    private func receiptPossibleTimer() {
        // タイマー
        let timeInterval = Double((self.settinsObject?.menuCheckSpan)!)
        Timer.scheduledTimer(timeInterval: timeInterval,
                             target: self,
                             selector: #selector(MenuViewController.receiptPossibleTimerUpdate),
                             userInfo: nil, repeats: true)
    }
    
    // 受付可否チェック
    @objc private func receiptPossibleTimerUpdate() {
        self.checkReceiptPossible()
    }
    // Beacon タイマー
    private func beaconTimerStart() {
        // タイマー
        let timeInterval = Double((self.settinsObject?.beaconSendSpan!)!)
        Timer.scheduledTimer(timeInterval: timeInterval,
                             target: self,
                             selector: #selector(MenuViewController.beaconTimerUpdate),
                             userInfo: nil, repeats: true)
        
    }
    
    // Beacon 情報送信
    @objc private func beaconTimerUpdate() {
        
        let locate = LocateObject()
        locate.uniqueId = UserData.getUniqueId()
        locate.beacons = self.beacons
        LocateManager().postBeacon(locate: locate) { (locateResult) in
            if locateResult.result {
                // OK
                print("Beacon 情報送信 OK:")
            } else {
                // NG
                print("Beacon 情報送信 NG: ")
            }
            
        }
        self.beacons = [LocateObject.Beacon]()
    }
    
    // 位置情報タイマー
    private func geoTimerStart() {
        // タイマー
        let timeInterval = Double((self.settinsObject?.geoSendSpan!)!)
        Timer.scheduledTimer(timeInterval: timeInterval,
                             target: self,
                             selector: #selector(MenuViewController.geoTimerUpdate),
                             userInfo: nil, repeats: true)
        
    }
    
    // 位置情報送信
    @objc private func geoTimerUpdate() {
        
        if let _ = self.geo {
            let locate = LocateObject()
            locate.uniqueId = UserData.getUniqueId()
            locate.geo = self.geo
            LocateManager().postGeo(locate: locate) { (locateResult) in
                if locateResult.result {
                    // OK
                    print("位置情報送信 OK: ")
                } else {
                    // NG
                    print("位置情報送信 NG: ")
                }
            }
            self.geo = nil
        }
    }
    
    // 設定の情報から画面を書き換える
    private func initDisplay () {
        // 受付可能時間
        if ((settinsObject?.result)!) {
            // error
            receptionPossibleLabel.text = "受付可能時間: \(settinsObject!.activeSendTime.start!) ~ \(settinsObject!.activeSendTime.end!)"
            
        } else {
            // error
            receptionPossibleLabel.text = "受付時間取得エラー"
        }
    }
    
    
    // ビーコンAPIテスト
    private func beacon() {
        let locate = LocateObject()
        locate.uniqueId = "12345678901234"//UserData.getUniqueId()
        var beacons:[LocateObject.Beacon] = [LocateObject.Beacon]()
        
        let beacon1 = LocateObject.Beacon()
        beacon1.time = "2018-09-27 17:00:00"
        beacon1.name = "test_beacon_1"
        beacon1.level = 30
        
        let beacon2 = LocateObject.Beacon()
        beacon2.time = "2018-09-28 18:00:00"
        beacon2.name = "test_beacon_2"
        beacon2.level = 40
        
        
        beacons.append(beacon1)
        beacons.append(beacon2)
        
        locate.beacons = beacons
        LocateManager().postBeacon(locate: locate) { (response) in
            print(response)
        }
    }
    
    @IBAction func registerTap(_ sender: Any) {
        self.performSegue(withIdentifier: MenuViewController.Segue.registerView.rawValue, sender: nil)
    }
    
    @IBAction func receptionTap(_ sender: Any) {
        self.performSegue(withIdentifier: MenuViewController.Segue.receptionView.rawValue, sender: nil)
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if MenuViewController.Segue.registerView.rawValue == segue.identifier {
            let navi = segue.destination as! UINavigationController
            let view = navi.viewControllers[0] as! RegisterViewController
            view.delegate = self
            openViewController = view
        } else if MenuViewController.Segue.receptionView.rawValue == segue.identifier {
            let navi = segue.destination as! UINavigationController
            let view = navi.viewControllers[0] as! ReceptionViewController
            view.delegate = self
            openViewController = view
        } else  {
            
        }
    }
    
    // プロトコル
    // 登録完了を、ビューから呼ばれる
    func registerd (resultObject:ReceiptObject) {
        
        let checkObject = CheckObject()
        checkObject.result = resultObject.result
        checkObject.button = resultObject.result ?"再登録":"登録"
        checkObject.message = resultObject.message
        if let createdAt = resultObject.createdAt {
            checkObject.message = "\(resultObject.message)\n\(createdAt)"
        }
        checkObject.checkFlg = true
        
        self.settingRegisterButton(checkObject: checkObject)

    }
    
    // 受付処理、ビューから呼ばれる
    func receipted(resultObject:ReceiptObject) {
        
        let checkObject = CheckObject()
        checkObject.result = resultObject.result
        checkObject.message = resultObject.message
        checkObject.checkFlg = resultObject.checkFlg

        self.receiptButtonEnable(resultCheck: checkObject)

    }
    
    /*
     // MARK: ビーコン
     */
    
    /*
     // MARK: - Navigation
     
     // In a storyboard-based application, you will often want to do a little preparation before navigation
     override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
     // Get the new view controller using segue.destination.
     // Pass the selected object to the new view controller.
     }
     */
    
}
