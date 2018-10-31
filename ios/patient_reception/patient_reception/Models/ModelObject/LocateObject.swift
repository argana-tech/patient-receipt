//
//  LocateObject.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import SwiftyJSON

class LocateObject: AbstractObject {
    var uniqueId:String?
    var beacons:[Beacon] = [Beacon]()
    var geo:Geo?
    
    class Beacon {
        var time:String?
        var name:String?
        var level:Int?
        
        func isSendBeacon () -> Bool {
            let flg = true
            return flg
        }
    }
    
    class Geo {
        var latitude:Double!
        var longitude:Double!
        

    }
    
    override func abstractSetData(json: JSON) {
        
    }
    

    

}

