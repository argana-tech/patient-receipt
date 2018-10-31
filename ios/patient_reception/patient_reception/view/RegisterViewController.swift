//
//  RegisterViewController.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/26.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import AVFoundation
import Firebase
import FirebaseMessaging
import MaterialComponents

class RegisterViewController: UIViewController, AVCaptureMetadataOutputObjectsDelegate {

    let captureSession = AVCaptureSession()
    var videoPreviewLayer:AVCaptureVideoPreviewLayer?
    var qrCodeFrameView:UIView?
    var uniqueId:String?
    
    var delegate:MenuViewControllerDelegaete?
    
    @IBOutlet weak var descriptionLabel: UILabel!
    
    @IBOutlet weak var registerButton: MDCRaisedButton!
    @IBOutlet weak var cancellButton: MDCFloatingButton!
    //    var cancellButton:UIButton?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        registerButton.setBackgroundColor(UIColor.flatSkyBlue)
        registerButton.setTitleColor(UIColor.white, for: UIControlState.normal)
        
        cancellButton.setBackgroundColor(UIColor.flatSkyBlue)
        cancellButton.setTitleColor(UIColor.white, for: UIControlState.normal)
        
        descriptionLabel.adjustsFontSizeToFitWidth = true
        
        
        
        readQR();
        
        // Do any additional setup after loading the view.
    }
    
    
    func readQR() {
        let deviceDiscoverySession = AVCaptureDevice.DiscoverySession(
            deviceTypes: [.builtInWideAngleCamera],
            mediaType: AVMediaType.video,
            position: .back)
        
        guard let captureDevice = deviceDiscoverySession.devices.first else {
            print("Failed to get the camera device")
            return
        }
        
        do {
            let input = try AVCaptureDeviceInput(device: captureDevice)
            
            captureSession.addInput(input)
            
            let captureMetadataOutput = AVCaptureMetadataOutput()
            captureSession.addOutput(captureMetadataOutput)
            
            captureMetadataOutput.setMetadataObjectsDelegate(self, queue: DispatchQueue.main)
            captureMetadataOutput.metadataObjectTypes = [AVMetadataObject.ObjectType.qr]
            
            videoPreviewLayer = AVCaptureVideoPreviewLayer.init(session: captureSession)
            videoPreviewLayer?.videoGravity = AVLayerVideoGravity.resizeAspectFill
            videoPreviewLayer?.frame = view.layer.bounds
            view.layer.addSublayer(videoPreviewLayer!)
            
            self.captureSession.startRunning()
            view.bringSubview(toFront: cancellButton)
            //            view.bringSubview(toFront: messageLabel)
            //            view.bringSubview(toFront: topbar)
            
            qrCodeFrameView = UIView()
            // スキャンしたQRコードを緑の枠で囲む
            if let qrCodeFrameView = qrCodeFrameView {
                qrCodeFrameView.layer.borderColor = UIColor.green.cgColor
                qrCodeFrameView.layer.borderWidth = 2
                view.addSubview(qrCodeFrameView)
                view.bringSubview(toFront: qrCodeFrameView)
            }
            
        } catch {
            print(error)
            return
        }
    }
    
    @IBAction func cancellButtonTap(_ sender: Any) {
        self.dismiss(animated: true) {
            self.captureSession.stopRunning()
        }
    }
    func metadataOutput(_ output: AVCaptureMetadataOutput, didOutput metadataObjects: [AVMetadataObject], from connection: AVCaptureConnection) {
        // Check if the metadataObjects array is not nil and it contains at least one object.
        if metadataObjects.count == 0 {
            qrCodeFrameView?.frame = CGRect.zero
//            messageLabel.text = "No QR code is detected"
            return
        }
        
        // Get the metadata object.
        let metadataObj = metadataObjects[0] as! AVMetadataMachineReadableCodeObject
        
        if metadataObj.type == AVMetadataObject.ObjectType.qr {
            // If the found metadata is equal to the QR code metadata then update the status label's text and set the bounds
            let barCodeObject = videoPreviewLayer?.transformedMetadataObject(for: metadataObj)
            qrCodeFrameView?.frame = barCodeObject!.bounds
            
            if metadataObj.stringValue != nil {
//                print(metadataObj.stringValue)
                uniqueId = metadataObj.stringValue!
//                uniqueId = "12345678901234"
                UserData.setUniqueId(uniqueId: uniqueId!)
                
                self.captureSession.stopRunning()
                cancellButton.isHidden = true
                qrCodeFrameView?.removeFromSuperview()
                videoPreviewLayer?.removeFromSuperlayer()
                
//                messageLabel.text = metadataObj.stringValue
            }
        }
    }

    @IBAction func registerTap(_ sender: Any) {
        // api 呼び出し
        let receiptObject = ReceiptObject()
        receiptObject.uniqueId = self.uniqueId
        receiptObject.deviceToken = InstanceID.instanceID().token()
//        InstanceID.instanceID().instanceID { (result, error) in
//            if let error = error {
//                print("Error fetching remote instange ID: \(error)")
//            } else if let result = result {f
//                receiptObject.deviceToken = result.token
//                print("Remote instance ID token: \(result.token)")
//            }
//        }
//
        ReceiptManager().postRegister(register: receiptObject) { (resultObject) in
            
            // 成功

            let controller = UIAlertController(title: "登録", message: resultObject.message, preferredStyle: UIAlertControllerStyle.alert)
            controller.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: { (action) in
                self.delegate?.registerd(resultObject: resultObject)
                self.dismiss(animated: true, completion: nil)
            }))
            self.present(controller, animated: true, completion: nil)
            
            
//            if (receiptObject.result) {
//
//
//
//            } else {
//                // 失敗
//                let controller = UIAlertController(title: "登録", message: "登録に失敗しました。", preferredStyle: UIAlertControllerStyle.alert)
//                controller.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: { (action) in
//                    self.delegate?.registerd(resultObject: receiptObject)
//                    self.dismiss(animated: true, completion: nil)
//                }))
//                self.present(controller, animated: true, completion: nil)
//
//            }
//
//
//
//
        }
        print("登録")
    }
    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destination.
        // Pass the selected object to the new view controller.
    }
    */

}
