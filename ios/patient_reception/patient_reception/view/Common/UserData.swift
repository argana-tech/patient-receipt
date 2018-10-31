//
//  UserData.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit

class UserData: NSObject {

    enum key:String {
        case uniqueId = "uniqueId"
    }
    
    static public func setUniqueId (uniqueId:String)  {
        let userDefaults = UserDefaults.standard
        userDefaults.set(uniqueId, forKey: UserData.key.uniqueId.rawValue)
        userDefaults.synchronize()
    }
    
    static public func getUniqueId () -> String {
        let userDefaults = UserDefaults.standard
        if let uniqueId =  userDefaults.object(forKey: UserData.key.uniqueId.rawValue) as? String {
            return uniqueId
        } else {
            return ""
        }
    }
}
