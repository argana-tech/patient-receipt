//
//  ReceiptObject.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import SwiftyJSON

class ReceiptObject: AbstractObject {

    var uniqueId:String?
    var deviceToken:String?
    
    var checkFlg:Bool = false
    var createdAt:String?
    var registedAt:String?
    
    override func abstractSetData(json: JSON) {
     
        let r = json[Response.checkFlag.rawValue].bool
        if let r = r {
            self.checkFlg = r
        }
        
        if let c = json[Response.Receipt.createdAt.rawValue].string {
            self.createdAt = c
        }
        
        if let regi = json[Response.Receipt.registedAt.rawValue].string {
             self.registedAt = regi
        }
    }
}
