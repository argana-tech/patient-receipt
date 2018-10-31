//
//  CheckObject.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import SwiftyJSON

class CheckObject: AbstractObject {
    var uniqueId:String?
    var checkFlg:Bool = false
    var button:String?
    
    override func abstractSetData(json: JSON) {
        
        let r = json[Response.checkFlag.rawValue].bool
        if let r = r {
            self.checkFlg = r
        }
        self.button = json[Response.button.rawValue].string
    }
}
