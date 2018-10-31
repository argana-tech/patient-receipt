//
//  ReceptionViewController.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/26.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit

class ReceptionViewController: UIViewController {
    
    var delegate:MenuViewControllerDelegaete?

    override func viewDidLoad() {
        super.viewDidLoad()
        
        let receiptObject = ReceiptObject()
        receiptObject.uniqueId = UserData.getUniqueId()
        ReceiptManager().postEntry(register: receiptObject) { (receiptResult) in
            var message = ""
            if receiptResult.result {
                // OK
                message = receiptResult.message
            } else {
                // NG
                message = receiptResult.message
            }
            
            let alertController = UIAlertController(title: "受付", message: message, preferredStyle: UIAlertControllerStyle.alert)
            let action = UIAlertAction(title: "受付", style: UIAlertActionStyle.default, handler: { (alertAction) in
                print("alert")
                self.delegate?.receipted(resultObject: receiptResult)
                self.dismiss(animated: true, completion: nil)
                
            })
            alertController.addAction(action)
            
            self.present(alertController, animated: true, completion: nil)

        }

        // Do any additional setup after loading the view.
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
