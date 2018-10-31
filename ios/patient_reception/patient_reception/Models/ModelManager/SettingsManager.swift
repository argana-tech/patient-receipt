//
//  SettingsManager.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/28.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON

class SettingsManager: AbstractManager<SettingsObject> {
//    class func getProerty (complitionHandler:@escaping (DataResponse<JSON>) -> Void) {
    func getProerty (complitionHandler:@escaping (SettingsObject) -> Void) {

        let url:String = Server.getUrl(api: .settings(action: .property))
        request(url, method: .get, params: nil, complitionHandler: complitionHandler)
//        Alamofire.request(url, method: .get).responseSwiftyJSON { (response) in
//            let settingResult =  SettingsObject()
//            settingResult.setData(response: response)
//
//            complitionHandler(settingResult)
//        }
    }
    
    override func createObject() -> AbstractObject {
        return SettingsObject()
    }
}
